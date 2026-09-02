<script setup>
import {
    ref,
    reactive,
    onMounted,
    onUnmounted,
    computed,
    watch,
    defineEmits,
    defineProps,
} from "vue";

// --- PROPS AND EMITS ---
const props = defineProps({
    fullscreenMode: {
        type: Boolean,
        default: false,
    },
});
const emit = defineEmits(["toggle-fullscreen"]);

// --- REFS FOR DOM ELEMENTS ---
const svgCanvas = ref(null);
const viewportEl = ref(null);
const minimapSvgEl = ref(null);
const rulerXEl = ref(null);
const rulerYEl = ref(null);
const bgUploadInput = ref(null);
const importJSONInput = ref(null);

// --- CORE REACTIVE STATE ---
const objects = ref([]);
const selected = ref([]);
const clipboard = ref(null);
const history = reactive({ stack: [], index: -1 });
const tool = ref("select");
const isSnap = ref(true);
const showGrid = ref(true);
const pxPerMeter = ref(60);
const gridMeters = ref(1);
const viewportTransform = reactive({ x: 0, y: 0, k: 1 });
const dragState = ref(null);
const contextMenu = reactive({ visible: false, x: 0, y: 0, targetId: null });
const bgImage = reactive({ src: null, opacity: 0.6 });
const autoArrangeOptions = reactive({ rows: 3, cols: 5 });
const isMinimapPanning = ref(false);
const minimapPanStart = ref(null);

let groupIdCounter = 0;

// --- COMPUTED PROPERTIES ---
const inspectorObject = computed(() => {
    if (selected.value.length === 1) {
        return objects.value.find((o) => o.id === selected.value[0]);
    }
    return null;
});

const inspectorProps = reactive({
    name: "",
    exhibitor: "",
    number: "",
    w: 0,
    h: 0,
    rot: 0,
    color: "#60a5fa",
    notes: "",
});

const infoBarText = computed(() => {
    return `Scale: ${pxPerMeter.value} px/m | Grid: ${
        gridMeters.value
    } m | Snap: ${isSnap.value ? "ON" : "OFF"} | Items: ${
        objects.value.length
    } | Selected: ${selected.value.length}`;
});

const miniMapData = computed(() => {
    const MAP_WIDTH = 200;
    const MAP_HEIGHT = 120;
    const getBBox = (o) => {
        if (o.type === "rect") return { x: o.x, y: o.y, w: o.w, h: o.h };
        if (o.type === "circle")
            return { x: o.cx - o.r, y: o.cy - o.r, w: o.r * 2, h: o.r * 2 };
        return { x: 0, y: 0, w: 0, h: 0 };
    };
    const defaultBounds = {
        minX: 0,
        minY: 0,
        contentWidth: 5000,
        contentHeight: 5000,
        scale: MAP_WIDTH / 5000,
    };
    const defaultState = {
        viewBox: `0 0 ${MAP_WIDTH} ${MAP_HEIGHT}`,
        items: [],
        viewFinder: { x: 0, y: 0, w: 0, h: 0 },
        bounds: defaultBounds,
    };
    if (objects.value.length === 0 || !viewportEl.value) {
        return defaultState;
    }
    let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
    objects.value.forEach((obj) => {
        const box = getBBox(obj);
        minX = Math.min(minX, box.x);
        minY = Math.min(minY, box.y);
        maxX = Math.max(maxX, box.x + box.w);
        maxY = Math.max(maxY, box.y + box.h);
    });
    minX -= 100;
    minY -= 100;
    maxX += 100;
    maxY += 100;
    const contentWidth = Math.max(1, maxX - minX);
    const contentHeight = Math.max(1, maxY - minY);
    const scale = Math.min(
        MAP_WIDTH / contentWidth,
        MAP_HEIGHT / contentHeight
    );
    const items = objects.value.map((obj) => {
        const box = getBBox(obj);
        const item = {
            id: obj.id,
            type: obj.type,
            x: (box.x - minX) * scale,
            y: (box.y - minY) * scale,
            w: Math.max(1, box.w * scale),
            h: Math.max(1, box.h * scale),
        };
        if (obj.type === "circle") {
            item.cx = (obj.cx - minX) * scale;
            item.cy = (obj.cy - minY) * scale;
            item.r = obj.r * scale;
        }
        return item;
    });
    const viewRect = viewportEl.value.getBoundingClientRect();
    const viewTopLeft = screenToSVGPoint(viewRect.left, viewRect.top);
    const viewFinder = {
        x: (viewTopLeft.x - minX) * scale,
        y: (viewTopLeft.y - minY) * scale,
        w: (viewRect.width / viewportTransform.k) * scale,
        h: (viewRect.height / viewportTransform.k) * scale,
    };
    return {
        viewBox: `0 0 ${MAP_WIDTH} ${MAP_HEIGHT}`,
        items,
        viewFinder,
        bounds: { minX, minY, contentWidth, contentHeight, scale },
    };
});
const miniMapViewBox = computed(() => miniMapData.value.viewBox);
const miniMapItems = computed(() => miniMapData.value.items);
const miniMapViewFinder = computed(() => miniMapData.value.viewFinder);

const rulerTicksX = computed(() => {
    if (!viewportEl.value) return [];
    const ticks = [];
    const viewWidth = viewportEl.value.clientWidth;
    const { k, x } = viewportTransform;
    const gridStepSVG = toPx(gridMeters.value);
    const visibleStartSVG = -x / k;
    const visibleEndSVG = (viewWidth - x) / k;
    let currentSVGPos = Math.ceil(visibleStartSVG / gridStepSVG) * gridStepSVG;
    while (currentSVGPos < visibleEndSVG) {
        const screenPos = currentSVGPos * k + x;
        const minSpacing = 40;
        if (
            ticks.length > 0 &&
            screenPos - ticks[ticks.length - 1].position < minSpacing
        ) {
            currentSVGPos += gridStepSVG;
            continue;
        }
        ticks.push({
            position: screenPos,
            label: `${(currentSVGPos / pxPerMeter.value).toFixed(0)}m`,
        });
        currentSVGPos += gridStepSVG;
    }
    return ticks;
});
const rulerTicksY = computed(() => {
    if (!viewportEl.value) return [];
    const ticks = [];
    const viewHeight = viewportEl.value.clientHeight;
    const { k, y } = viewportTransform;
    const RULER_OFFSET = 24;
    const gridStepSVG = toPx(gridMeters.value);
    const visibleStartSVG = -y / k;
    const visibleEndSVG = (viewHeight - y) / k;
    let currentSVGPos = Math.ceil(visibleStartSVG / gridStepSVG) * gridStepSVG;
    while (currentSVGPos < visibleEndSVG) {
        const screenPos = currentSVGPos * k + y + RULER_OFFSET;
        const minSpacing = 30;
        if (
            ticks.length > 0 &&
            screenPos - ticks[ticks.length - 1].position < minSpacing
        ) {
            currentSVGPos += gridStepSVG;
            continue;
        }
        ticks.push({
            position: screenPos,
            label: `${(currentSVGPos / pxPerMeter.value).toFixed(0)}m`,
        });
        currentSVGPos += gridStepSVG;
    }
    return ticks;
});

const gridStyle = computed(() => {
    // If the grid is turned off, return an empty object
    if (!showGrid.value) return {};

    const gridPx = toPx(gridMeters.value);
    const zoom = viewportTransform.k;

    // These are Tailwind's default "slate" colors for dark/light mode consistency
    const lightColor = "rgba(226, 232, 240, 1)"; // slate-200
    const darkColor = "rgba(51, 65, 85, 1)"; // slate-700

    return {
        // Use CSS variables to handle dark mode automatically for the grid lines
        "--grid-color": `var(--tw-dark, ${lightColor})`,
        "--grid-color-dark": `var(--tw-dark, ${darkColor})`,

        // Generate a grid using CSS gradients
        backgroundImage: `
            linear-gradient(var(--grid-color) 1px, transparent 1px),
            linear-gradient(90deg, var(--grid-color) 1px, transparent 1px)
        `,
        // Make the grid size and position reactive to zoom and pan
        backgroundSize: `${gridPx * zoom}px ${gridPx * zoom}px`,
        backgroundPosition: `${viewportTransform.x}px ${viewportTransform.y}px`,
    };
});

// --- WATCHERS ---
watch(
    () => inspectorProps.color,
    (newColor) => {
        if (inspectorObject.value) {
            inspectorObject.value.fill = newColor;
        }
    }
);
watch(inspectorObject, (newObj) => {
    if (newObj) {
        inspectorProps.name = newObj.meta.name || "";
        inspectorProps.exhibitor = newObj.meta.exhibitor || "";
        inspectorProps.number = newObj.meta.number || "";
        inspectorProps.notes = newObj.meta.notes || "";
        if (newObj.type === "rect") {
            inspectorProps.w = (newObj.w / pxPerMeter.value).toFixed(2);
            inspectorProps.h = (newObj.h / pxPerMeter.value).toFixed(2);
        } else if (newObj.type === "circle") {
            inspectorProps.w = ((newObj.r * 2) / pxPerMeter.value).toFixed(2);
            inspectorProps.h = ((newObj.r * 2) / pxPerMeter.value).toFixed(2);
        }
        inspectorProps.rot = newObj.rot || 0;
        inspectorProps.color = newObj.fill || "#60a5fa";
    }
});

// --- UTILITY & CORE FUNCTIONS ---
const uid = (pref = "i") =>
    `${pref}_${Date.now().toString(36)}_${Math.random()
        .toString(36)
        .slice(2, 7)}`;
const toPx = (meters) => meters * pxPerMeter.value;
const toMeters = (px) => px / pxPerMeter.value;
const snapToGrid = (val) => {
    if (!isSnap.value) return val;
    const gridPx = toPx(gridMeters.value);
    return Math.round(val / gridPx) * gridPx;
};
const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

// --- HISTORY ---
const pushHistory = (label = "edit") => {
    const snapshot = JSON.stringify(objects.value);
    history.stack = history.stack.slice(0, history.index + 1);
    history.stack.push({ label, snapshot });
    history.index++;
    if (history.stack.length > 80) {
        history.stack.shift();
        history.index--;
    }
};
const undo = () => {
    if (history.index <= 0) return;
    history.index--;
    objects.value = JSON.parse(history.stack[history.index].snapshot);
};
const redo = () => {
    if (history.index >= history.stack.length - 1) return;
    history.index++;
    objects.value = JSON.parse(history.stack[history.index].snapshot);
};

// --- SVG & OBJECT MANIPULATION ---
const screenToSVGPoint = (clientX, clientY) => {
    if (!svgCanvas.value) return { x: 0, y: 0 };
    const pt = svgCanvas.value.createSVGPoint();
    pt.x = clientX;
    pt.y = clientY;
    const screenCTM = svgCanvas.value.getScreenCTM();
    return screenCTM ? pt.matrixTransform(screenCTM.inverse()) : pt;
};
const findById = (id) => objects.value.find((o) => o.id === id);
const deselectAll = () => (selected.value = []);
const selectIds = (ids, append = false) => {
    if (!append) selected.value = ids.slice();
    else selected.value = Array.from(new Set([...selected.value, ...ids]));
};
const deleteSelected = () => {
    if (selected.value.length === 0) return;
    objects.value = objects.value.filter((o) => !selected.value.includes(o.id));
    selected.value = [];
    pushHistory("delete");
};
const duplicateSelected = () => {
    if (!selected.value.length) return;
    const newIds = [];
    selected.value.forEach((id) => {
        const o = findById(id);
        if (!o) return;
        const copy = JSON.parse(JSON.stringify(o));
        copy.id = uid(o.type);
        if (copy.x !== undefined) {
            copy.x += 20;
            copy.y += 20;
        } else if (copy.cx !== undefined) {
            copy.cx += 20;
            copy.cy += 20;
        }
        objects.value.push(copy);
        newIds.push(copy.id);
    });
    pushHistory("duplicate");
    selectIds(newIds);
};
const createRectModel = (x, y, w, h, opts = {}) => ({
    id: uid("rect"),
    type: "rect",
    x,
    y,
    w,
    h,
    rot: 0,
    fill: opts.fill || "#60a5fa",
    stroke: "#243b53",
    locked: false,
    meta: {
        name: opts.name || "Booth",
        exhibitor: opts.exhibitor || "Unassigned",
        number: "",
        notes: "",
    },
    group: null,
});
const createCircleModel = (cx, cy, radius, opts = {}) => ({
    id: uid("circle"),
    type: "circle",
    cx,
    cy,
    r: radius,
    rot: 0,
    fill: opts.fill || "#f59e0b",
    stroke: "#243b53",
    locked: false,
    meta: {
        name: opts.name || "Feature",
        exhibitor: "",
        number: "",
        notes: "",
    },
    group: null,
});

// --- UI ACTIONS ---
const setTool = (newTool) => (tool.value = newTool);
const applyProps = () => {
    if (!inspectorObject.value) return;
    const obj = inspectorObject.value;
    obj.meta.name = inspectorProps.name;
    obj.meta.exhibitor = inspectorProps.exhibitor;
    obj.meta.number = inspectorProps.number;
    obj.meta.notes = inspectorProps.notes;
    obj.fill = inspectorProps.color;
    obj.rot = Number(inspectorProps.rot) || 0;
    if (obj.type === "rect") {
        obj.w = Math.max(6, toPx(Number(inspectorProps.w) || 0.1));
        obj.h = Math.max(6, toPx(Number(inspectorProps.h) || 0.1));
    } else if (obj.type === "circle") {
        const d = Math.max(6, toPx(Number(inspectorProps.w) || 0.1));
        obj.r = d / 2;
    }
    pushHistory("edit props");
};
const addPreset = (preset) => {
    const center = { x: 400, y: 200 };
    let model;
    if (preset === "2x2")
        model = createRectModel(
            center.x - toPx(1),
            center.y - toPx(1),
            toPx(2),
            toPx(2),
            { name: "2x2 Booth" }
        );
    if (preset === "3x3")
        model = createRectModel(
            center.x - toPx(1.5),
            center.y - toPx(1.5),
            toPx(3),
            toPx(3),
            { name: "3x3 Booth" }
        );
    if (preset === "4x3")
        model = createRectModel(
            center.x - toPx(2),
            center.y - toPx(1.5),
            toPx(4),
            toPx(3),
            { name: "4x3 Booth" }
        );
    if (preset === "island")
        model = createRectModel(
            center.x - toPx(3),
            center.y - toPx(3),
            toPx(6),
            toPx(6),
            { name: "Island" }
        );
    if (model) {
        objects.value.push(model);
        pushHistory("add preset");
    }
};
const autoArrange = () => {
    if (!confirm("This will clear existing booths. Proceed?")) return;
    objects.value = objects.value.filter(
        (o) => !["rect", "circle"].includes(o.type)
    );
    const rows = Math.max(1, autoArrangeOptions.rows);
    const cols = Math.max(1, autoArrangeOptions.cols);
    const boothW = toPx(3);
    const boothH = toPx(3);
    const gap = toPx(1.5);
    const startX = 50,
        startY = 50;
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const x = startX + c * (boothW + gap);
            const y = startY + r * (boothH + gap);
            objects.value.push(
                createRectModel(x, y, boothW, boothH, {
                    name: `B-${r + 1}-${c + 1}`,
                })
            );
        }
    }
    pushHistory("auto arrange");
};
const clearAll = () => {
    if (confirm("Are you sure you want to clear the entire canvas?")) {
        objects.value = [];
        selected.value = [];
        pushHistory("clear all");
    }
};
const handleBgUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => (bgImage.src = e.target.result);
        reader.readAsDataURL(file);
    }
};
const exportJSON = () => {
    const data = {
        objects: objects.value,
        pxPerMeter: pxPerMeter.value,
        gridMeters: gridMeters.value,
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], {
        type: "application/json",
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "floorplan.json";
    a.click();
    URL.revokeObjectURL(url);
};
const loadJSON = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const data = JSON.parse(e.target.result);
                if (data.objects) objects.value = data.objects;
                if (data.pxPerMeter) pxPerMeter.value = data.pxPerMeter;
                if (data.gridMeters) gridMeters.value = data.gridMeters;
                pushHistory("load json");
            } catch (err) {
                alert("Invalid JSON file.");
            }
        };
        reader.readAsText(file);
    }
};
const setZoom = (factor, center) => {
    const oldK = viewportTransform.k;
    const newK = clamp(oldK * factor, 0.2, 5);
    if (!center) {
        const viewRect = viewportEl.value.getBoundingClientRect();
        center = screenToSVGPoint(
            viewRect.left + viewRect.width / 2,
            viewRect.top + viewRect.height / 2
        );
    }
    viewportTransform.x =
        center.x - (center.x - viewportTransform.x) * (newK / oldK);
    viewportTransform.y =
        center.y - (center.y - viewportTransform.y) * (newK / oldK);
    viewportTransform.k = newK;
};
const zoomIn = () => setZoom(1.2);
const zoomOut = () => setZoom(1 / 1.2);
const fitToView = () => {
    viewportTransform.x = 0;
    viewportTransform.y = 0;
    viewportTransform.k = 1;
};
const onWheel = (event) => {
    const zoomFactor = event.deltaY < 0 ? 1.1 : 1 / 1.1;
    const pointer = screenToSVGPoint(event.clientX, event.clientY);
    setZoom(zoomFactor, pointer);
};
const bringForward = () => {
    if (!inspectorObject.value) return;
    const id = inspectorObject.value.id;
    const currentIndex = objects.value.findIndex((o) => o.id === id);
    if (currentIndex > -1 && currentIndex < objects.value.length - 1) {
        const nextElement = objects.value[currentIndex + 1];
        objects.value[currentIndex + 1] = inspectorObject.value;
        objects.value[currentIndex] = nextElement;
        pushHistory("Bring Forward");
    }
};
const sendBack = () => {
    if (!inspectorObject.value) return;
    const id = inspectorObject.value.id;
    const currentIndex = objects.value.findIndex((o) => o.id === id);
    if (currentIndex > 0) {
        const prevElement = objects.value[currentIndex - 1];
        objects.value[currentIndex - 1] = inspectorObject.value;
        objects.value[currentIndex] = prevElement;
        pushHistory("Send Back");
    }
};
const toggleLock = () => {
    if (inspectorObject.value) {
        inspectorObject.value.locked = !inspectorObject.value.locked;
        pushHistory(inspectorObject.value.locked ? "Lock" : "Unlock");
    }
};
const panToMinimapPoint = (event) => {
    if (!minimapSvgEl.value || !viewportEl.value) return;
    const { scale, minX, minY } = miniMapData.value.bounds;
    if (!scale) return;
    const mapRect = minimapSvgEl.value.getBoundingClientRect();
    const clickX = event.clientX - mapRect.left;
    const clickY = event.clientY - mapRect.top;
    const targetSvgX = clickX / scale + minX;
    const targetSvgY = clickY / scale + minY;
    const viewRect = viewportEl.value.getBoundingClientRect();
    viewportTransform.x = viewRect.width / 2 - targetSvgX * viewportTransform.k;
    viewportTransform.y =
        viewRect.height / 2 - targetSvgY * viewportTransform.k;
};
const onMinimapMouseDown = (event) => {
    isMinimapPanning.value = true;
    panToMinimapPoint(event);
};
const onMinimapMouseMove = (event) => {
    if (isMinimapPanning.value) {
        panToMinimapPoint(event);
    }
};
const exportPNG = async () => {
    if (objects.value.length === 0) {
        alert("Canvas is empty. Add some shapes to export.");
        return;
    }
    const getBBox = (o) => {
        if (o.type === "rect") return { x: o.x, y: o.y, w: o.w, h: o.h };
        if (o.type === "circle")
            return { x: o.cx - o.r, y: o.cy - o.r, w: o.r * 2, h: o.r * 2 };
        return { x: 0, y: 0, w: 0, h: 0 };
    };
    let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
    objects.value.forEach((obj) => {
        const box = getBBox(obj);
        minX = Math.min(minX, box.x);
        minY = Math.min(minY, box.y);
        maxX = Math.max(maxX, box.x + box.w);
        maxY = Math.max(maxY, box.y + box.h);
    });
    const padding = 50;
    const imageWidth = maxX - minX + padding * 2;
    const imageHeight = maxY - minY + padding * 2;
    const objectsSVG = objects.value
        .map((obj) => {
            const commonStroke = 'stroke="#243b53" stroke-width="1"';
            if (obj.type === "rect") {
                const transform = `transform="translate(${
                    obj.x - minX + padding
                }, ${obj.y - minY + padding}) rotate(${obj.rot}, ${
                    obj.w / 2
                }, ${obj.h / 2})"`;
                const rect = `<rect width="${obj.w}" height="${obj.h}" fill="${obj.fill}" ${commonStroke} />`;
                const text = `<text x="${obj.w / 2}" y="${
                    obj.h / 2
                }" dominant-baseline="middle" text-anchor="middle" style="font-size:12px; fill:#0f172a; font-family: sans-serif;">${
                    obj.meta.name || ""
                }</text>`;
                return `<g ${transform}>${rect}${text}</g>`;
            }
            if (obj.type === "circle") {
                const transform = `transform="translate(${
                    obj.cx - minX + padding
                }, ${obj.cy - minY + padding}) rotate(${obj.rot})"`;
                const circle = `<circle r="${obj.r}" fill="${obj.fill}" ${commonStroke} />`;
                const text = `<text dominant-baseline="middle" text-anchor="middle" style="font-size:12px; fill:#0f172a; font-family: sans-serif;">${
                    obj.meta.name || ""
                }</text>`;
                return `<g ${transform}>${circle}${text}</g>`;
            }
            return "";
        })
        .join("");
    const finalSVG = `<svg width="${imageWidth}" height="${imageHeight}" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="white" />${objectsSVG}</svg>`;
    const svgBlob = new Blob([finalSVG], {
        type: "image/svg+xml;charset=utf-8",
    });
    const url = URL.createObjectURL(svgBlob);
    const img = new Image();
    img.onload = () => {
        const canvas = document.createElement("canvas");
        canvas.width = imageWidth;
        canvas.height = imageHeight;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        URL.revokeObjectURL(url);
        const a = document.createElement("a");
        a.href = canvas.toDataURL("image/png");
        a.download = "floorplan.png";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    img.onerror = (err) => {
        console.error("Failed to load SVG for PNG conversion.", err);
        alert("Sorry, there was an error exporting the image.");
        URL.revokeObjectURL(url);
    };
    img.src = url;
};

// --- CORRECT FULLSCREEN TOGGLE FUNCTION ---
const toggleFullscreen = () => emit("toggle-fullscreen");

// --- LIFECYCLE HOOKS & EVENT HANDLERS (REFACTORED FOR CLEANUP) ---
let isPanning = false;
let panStart = null;

const onMouseDown = (ev) => {
    if (ev.target.closest(".panel, .inspector, .topbar, .minimap")) return;
    const pt = screenToSVGPoint(ev.clientX, ev.clientY);
    if (ev.button === 1 || (ev.button === 0 && ev.ctrlKey)) {
        isPanning = true;
        panStart = {
            x: ev.clientX,
            y: ev.clientY,
            sx: viewportTransform.x,
            sy: viewportTransform.y,
        };
        if (svgCanvas.value) svgCanvas.value.style.cursor = "grabbing";
        return;
    }
    const targetId = ev.target.closest("[data-id]")?.dataset.id;
    if (tool.value === "select") {
        if (targetId) {
            const obj = findById(targetId);
            if (!obj) return;
            if (ev.shiftKey) {
                selected.value = selected.value.includes(targetId)
                    ? selected.value.filter((id) => id !== targetId)
                    : [...selected.value, targetId];
            } else if (!selected.value.includes(targetId)) {
                selectIds([targetId]);
            }
            if (!obj.locked) {
                dragState.value = {
                    type: "drag",
                    id: targetId,
                    startPt: pt,
                    original: JSON.parse(JSON.stringify(obj)),
                };
            }
        } else if (!ev.shiftKey) {
            deselectAll();
        }
    } else if (["rect", "circle"].includes(tool.value)) {
        dragState.value = {
            type: "create",
            tool: tool.value,
            start: pt,
            end: pt,
        };
    }
};
const onMouseMove = (ev) => {
    const pt = screenToSVGPoint(ev.clientX, ev.clientY);
    if (isPanning && panStart) {
        const dx = ev.clientX - panStart.x,
            dy = ev.clientY - panStart.y;
        viewportTransform.x = panStart.sx + dx;
        viewportTransform.y = panStart.sy + dy;
        return;
    }
    if (!dragState.value) return;
    if (dragState.value.type === "create") {
        dragState.value.end = pt;
    } else if (dragState.value.type === "drag") {
        const obj = findById(dragState.value.id);
        if (!obj || obj.locked) return;
        const dx = pt.x - dragState.value.startPt.x;
        const dy = pt.y - dragState.value.startPt.y;
        if (obj.type === "rect") {
            obj.x = snapToGrid(dragState.value.original.x + dx);
            obj.y = snapToGrid(dragState.value.original.y + dy);
        } else if (obj.type === "circle") {
            obj.cx = snapToGrid(dragState.value.original.cx + dx);
            obj.cy = snapToGrid(dragState.value.original.cy + dy);
        }
    }
};
const onMouseUp = (ev) => {
    isMinimapPanning.value = false;
    isPanning = false;
    if (svgCanvas.value) svgCanvas.value.style.cursor = "default";
    if (!dragState.value) return;
    if (dragState.value.type === "create") {
        const { start, end, tool } = dragState.value;
        if (tool === "rect") {
            const w = Math.abs(end.x - start.x);
            const h = Math.abs(end.y - start.y);
            if (w > 5 && h > 5) {
                const model = createRectModel(
                    snapToGrid(Math.min(start.x, end.x)),
                    snapToGrid(Math.min(start.y, end.y)),
                    snapToGrid(w),
                    snapToGrid(h)
                );
                objects.value.push(model);
                pushHistory("create");
                selectIds([model.id]);
            }
        } else if (tool === "circle") {
            const r = Math.hypot(end.x - start.x, end.y - start.y);
            if (r > 3) {
                const model = createCircleModel(
                    start.x,
                    start.y,
                    snapToGrid(r)
                );
                objects.value.push(model);
                pushHistory("create");
                selectIds([model.id]);
            }
        }
    } else if (dragState.value.type === "drag") {
        pushHistory("drag");
    }
    dragState.value = null;
};
const onKeyDown = (ev) => {
    if (ev.key === "Escape" && props.fullscreenMode) {
        toggleFullscreen();
        return;
    }
    if (
        document.activeElement.tagName === "INPUT" ||
        document.activeElement.tagName === "TEXTAREA"
    )
        return;
    if (ev.key === "Delete" || ev.key === "Backspace") deleteSelected();
    if ((ev.ctrlKey || ev.metaKey) && ev.key === "c") {
        if (selected.value.length > 0) {
            clipboard.value = selected.value.map((id) =>
                JSON.parse(JSON.stringify(findById(id)))
            );
        }
    }
    if ((ev.ctrlKey || ev.metaKey) && ev.key === "v") {
        if (!clipboard.value) return;
        const newIds = [];
        clipboard.value.forEach((item) => {
            const copy = JSON.parse(JSON.stringify(item));
            copy.id = uid(item.type);
            if (copy.x !== undefined) {
                copy.x += 20;
                copy.y += 20;
            } else if (copy.cx !== undefined) {
                copy.cx += 20;
                copy.cy += 20;
            }
            objects.value.push(copy);
            newIds.push(copy.id);
        });
        pushHistory("paste");
        selectIds(newIds);
    }
    if ((ev.ctrlKey || ev.metaKey) && ev.key === "z") undo();
    if ((ev.ctrlKey || ev.metaKey) && ev.key === "y") redo();
};

// --- REPLACE your existing onMounted with this complete version ---
onMounted(() => {
    // Seed data and other setup is unchanged
    const c = { x: 500, y: 300 };
    objects.value.push(
        createRectModel(c.x - toPx(3), c.y - toPx(1), toPx(3), toPx(2), {
            name: "Booth A",
            exhibitor: "TechCorp",
        })
    );
    objects.value.push(
        createRectModel(c.x + toPx(1), c.y - toPx(1), toPx(2), toPx(2), {
            name: "Booth B",
            exhibitor: "FoodInc",
        })
    );
    objects.value.push(
        createCircleModel(c.x + toPx(5), c.y + toPx(0.5), toPx(1.2), {
            name: "Info Desk",
        })
    );
    pushHistory("initial");

    // --- MOUSE EVENT HANDLERS ---
    let isPanning = false;
    let panStart = null;

    const onMouseDown = (ev) => {
        if (ev.target.closest(".panel, .inspector, .topbar, .minimap")) return;

        const pt = screenToSVGPoint(ev.clientX, ev.clientY);
        const targetId = ev.target.closest("[data-id]")?.dataset.id;

        // --- THIS IS THE CORRECTED LOGIC ---
        // Step 1: Handle deselection if clicking on the background.
        if (!targetId && tool.value === "select" && !ev.shiftKey) {
            deselectAll();
        }

        // Step 2: Now, check if we should start a pan.
        if (
            ev.button === 1 ||
            tool.value === "pan" ||
            (tool.value === "select" && !targetId)
        ) {
            isPanning = true;
            panStart = {
                x: ev.clientX,
                y: ev.clientY,
                sx: viewportTransform.x,
                sy: viewportTransform.y,
            };
            if (svgCanvas.value) {
                svgCanvas.value.style.cursor = "grabbing";
            }
            return;
        }
        // --- END OF CORRECTION ---

        if (tool.value === "select") {
            if (targetId) {
                const obj = findById(targetId);
                if (!obj) return;
                if (ev.shiftKey) {
                    selected.value = selected.value.includes(targetId)
                        ? selected.value.filter((id) => id !== targetId)
                        : [...selected.value, targetId];
                } else if (!selected.value.includes(targetId)) {
                    selectIds([targetId]);
                }
                if (!obj.locked) {
                    dragState.value = {
                        type: "drag",
                        id: targetId,
                        startPt: pt,
                        original: JSON.parse(JSON.stringify(obj)),
                    };
                }
            }
        } else if (["rect", "circle"].includes(tool.value)) {
            dragState.value = {
                type: "create",
                tool: tool.value,
                start: pt,
                end: pt,
            };
        }
    };

    const onMouseMove = (ev) => {
        // This function remains unchanged
        if (isPanning && panStart) {
            const dx = ev.clientX - panStart.x,
                dy = ev.clientY - panStart.y;
            viewportTransform.x = panStart.sx + dx;
            viewportTransform.y = panStart.sy + dy;
            return;
        }
        if (!dragState.value) return;
        const pt = screenToSVGPoint(ev.clientX, ev.clientY);
        if (dragState.value.type === "create") {
            dragState.value.end = pt;
        } else if (dragState.value.type === "drag") {
            const obj = findById(dragState.value.id);
            if (!obj || obj.locked) return;
            const dx = pt.x - dragState.value.startPt.x,
                dy = pt.y - dragState.value.startPt.y;
            if (obj.type === "rect") {
                obj.x = snapToGrid(dragState.value.original.x + dx);
                obj.y = snapToGrid(dragState.value.original.y + dy);
            } else if (obj.type === "circle") {
                obj.cx = snapToGrid(dragState.value.original.cx + dx);
                obj.cy = snapToGrid(dragState.value.original.cy + dy);
            }
        }
    };

    const onMouseUp = (ev) => {
        // This function remains unchanged
        isMinimapPanning.value = false;
        isPanning = false;
        // Reset cursor based on the current tool
        if (svgCanvas.value) {
            svgCanvas.value.style.cursor =
                tool.value === "pan"
                    ? "grab"
                    : tool.value === "select"
                    ? "default"
                    : "crosshair";
        }
        if (!dragState.value) return;
        if (dragState.value.type === "create") {
            const { start, end, tool: creationTool } = dragState.value;
            if (creationTool === "rect") {
                const w = Math.abs(end.x - start.x),
                    h = Math.abs(end.y - start.y);
                if (w > 5 && h > 5) {
                    const model = createRectModel(
                        snapToGrid(Math.min(start.x, end.x)),
                        snapToGrid(Math.min(start.y, end.y)),
                        snapToGrid(w),
                        snapToGrid(h)
                    );
                    objects.value.push(model);
                    pushHistory("create");
                    selectIds([model.id]);
                }
            } else if (creationTool === "circle") {
                const r = Math.hypot(end.x - start.x, end.y - start.y);
                if (r > 3) {
                    const model = createCircleModel(
                        start.x,
                        start.y,
                        snapToGrid(r)
                    );
                    objects.value.push(model);
                    pushHistory("create");
                    selectIds([model.id]);
                }
            }
        } else if (dragState.value.type === "drag") pushHistory("drag");
        dragState.value = null;
    };

    // Attach global listeners (unchanged)
    window.addEventListener("mousedown", onMouseDown);
    window.addEventListener("mousemove", onMouseMove);
    window.addEventListener("mouseup", onMouseUp);
    window.addEventListener("keydown", onKeyDown);
});
</script>

<template>
    <div
        class="relative flex w-full overflow-hidden bg-gray-100 dark:bg-gray-900/50 font-sans text-sm text-gray-800 dark:text-gray-300 gap-6"
        :class="{
            'h-[calc(100vh-200px)]': !fullscreenMode,
            'fixed inset-0 z-[9999] h-screen w-screen p-6 bg-gray-100 dark:bg-gray-900':
                fullscreenMode,
        }"
    >
        <!-- Left Palette -->
        <aside
            class="w-[300px] flex-shrink-0 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-3 overflow-y-auto hidden lg:block panel [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-slate-700 [&::-webkit-scrollbar-thumb]:hover:bg-gray-400 dark:[&::-webkit-scrollbar-thumb]:hover:bg-slate-500"
        >
            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Tools
            </h3>
            <div class="grid grid-cols-3 gap-1.5 mt-2">
                <button
                    @click="setTool('select')"
                    :class="{
                        'bg-brand-500 text-white': tool === 'select',
                        'hover:bg-gray-100 dark:hover:bg-gray-800':
                            tool !== 'select',
                    }"
                    class="p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-pointer"
                >
                    Select
                </button>
                <button
                    @click="setTool('rect')"
                    :class="{
                        'bg-brand-500 text-white': tool === 'rect',
                        'hover:bg-gray-100 dark:hover:bg-gray-800':
                            tool !== 'rect',
                    }"
                    class="p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-pointer"
                >
                    Rect
                </button>
                <button
                    @click="setTool('circle')"
                    :class="{
                        'bg-brand-500 text-white': tool === 'circle',
                        'hover:bg-gray-100 dark:hover:bg-gray-800':
                            tool !== 'circle',
                    }"
                    class="p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-pointer"
                >
                    Circle
                </button>
                <button
                    @click="setTool('pan')"
                    :class="{
                        'bg-brand-500 text-white': tool === 'pan',
                        'hover:bg-gray-100 dark:hover:bg-gray-800':
                            tool !== 'pan',
                    }"
                    class="p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-pointer"
                >
                    Pan
                </button>
            </div>

            <div class="mt-4">
                <label
                    class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
                    >Presets</label
                >
                <div class="grid grid-cols-2 gap-1.5 mt-2">
                    <button
                        @click="addPreset('2x2')"
                        class="p-2 text-center rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-grab hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        2m x 2m
                    </button>
                    <button
                        @click="addPreset('3x3')"
                        class="p-2 text-center rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-grab hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        3m x 3m
                    </button>
                    <button
                        @click="addPreset('4x3')"
                        class="p-2 text-center rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-grab hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        4m x 3m
                    </button>
                    <button
                        @click="addPreset('island')"
                        class="p-2 text-center rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 cursor-grab hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        Island 6x6
                    </button>
                </div>
            </div>

            <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>

            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Grid & View
            </h3>
            <div class="space-y-2 mt-2">
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Scale (px per meter)</label
                    >
                    <input
                        v-model.number="pxPerMeter"
                        type="number"
                        min="10"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Grid size (meters)</label
                    >
                    <select
                        v-model.number="gridMeters"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                        <option value="0.5">0.5 m</option>
                        <option value="1">1 m</option>
                        <option value="2">2 m</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-1">
                    <button
                        @click="showGrid = !showGrid"
                        class="flex-1 text-xs p-1.5 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"
                    >
                        Toggle Grid
                    </button>
                    <button
                        @click="isSnap = !isSnap"
                        class="flex-1 text-xs p-1.5 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"
                    >
                        Snap: {{ isSnap ? "On" : "Off" }}
                    </button>
                </div>
            </div>

            <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>

            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Background
            </h3>
            <div class="space-y-2 mt-2">
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Upload Hall Background</label
                    >
                    <input
                        type="file"
                        @change="handleBgUpload"
                        accept="image/*"
                        class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-gray-700 dark:file:text-gray-300"
                    />
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Background opacity</label
                    >
                    <input
                        v-model.number="bgImage.opacity"
                        type="range"
                        min="0"
                        max="1"
                        step="0.05"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                    />
                </div>
            </div>

            <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>

            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Auto Arrange
            </h3>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <div>
                    <label class="block mb-1 text-xs text-gray-500">Rows</label>
                    <input
                        v-model.number="autoArrangeOptions.rows"
                        type="number"
                        min="1"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500">Cols</label>
                    <input
                        v-model.number="autoArrangeOptions.cols"
                        type="number"
                        min="1"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
            </div>
            <div class="flex gap-2 mt-2">
                <button
                    @click="autoArrange"
                    class="flex-1 p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"
                >
                    Auto Arrange
                </button>
                <button
                    @click="clearAll"
                    class="flex-1 p-2 rounded-md bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:bg-red-500/10 hover:border-red-500/20 hover:text-red-600"
                >
                    Clear All
                </button>
            </div>

            <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>

            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Export / Save
            </h3>
            <div class="flex gap-2 mt-2">
                <!-- ADD THIS EXPORT PNG BUTTON -->
                <button
                    @click="exportPNG"
                    class="shadow-sm hover:bg-gray-100 dark:hover:bg-gray-800 flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 transition"
                >
                    Export PNG
                </button>
                <button
                    @click="exportJSON"
                    class="shadow-sm hover:bg-brand-600 bg-brand-500 flex-1 rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                >
                    Save JSON
                </button>
            </div>
            <div class="mt-2">
                <label class="block mb-1 text-xs text-gray-500"
                    >Load from JSON</label
                >
                <input
                    type="file"
                    @change="loadJSON"
                    accept="application/json"
                    class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-700 dark:file:text-gray-300"
                />
            </div>

            <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>
            <div class="text-xs text-gray-400 p-1">
                Shortcuts: Delete, Ctrl+C/V, Z/Y
            </div>
        </aside>

        <!-- Main Workspace -->
        <main
            class="flex-1 flex flex-col min-w-0 h-full relative rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-900 overflow-hidden"
        >
            <div
                class="h-12 flex-shrink-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800 flex items-center px-4 gap-2"
            >
                <div class="font-semibold text-gray-800 dark:text-white">
                    <span class="text-xs text-gray-500">{{ infoBarText }}</span>
                </div>
                <div class="ml-auto flex gap-2 items-center">
                    <!-- ADD THIS BLOCK FOR ZOOM CONTROLS -->
                    <button
                        @click="zoomIn"
                        class="px-2 py-1 rounded-md text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700/50"
                    >
                        Zoom +
                    </button>
                    <button
                        @click="zoomOut"
                        class="px-2 py-1 rounded-md text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700/50"
                    >
                        Zoom -
                    </button>
                    <button
                        @click="fitToView"
                        class="px-2 py-1 rounded-md text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700/50"
                    >
                        Fit
                    </button>
                    <button
                        @click="toggleFullscreen"
                        class="px-2 py-1 rounded-md text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700/50 w-20"
                    >
                        {{ fullscreenMode ? "Exit" : "Fullscreen" }}
                    </button>

                    <!-- END OF BLOCK -->
                </div>
            </div>

            <!-- REVISED: Add Rulers and offset the viewport -->
            <div
                @wheel.prevent="onWheel"
                class="flex-1 min-h-0 overflow-hidden relative bg-gray-50 dark:bg-gray-900/50"
            >
                <!-- RULER - X (TOP) -->
                <div
                    class="absolute top-0 left-6 h-6 right-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-10 overflow-hidden border-b border-gray-200 dark:border-gray-800"
                >
                    <div
                        v-for="tick in rulerTicksX"
                        :key="tick.label"
                        class="absolute top-0 text-[10px] text-gray-500 dark:text-gray-400"
                        :style="{ left: tick.position + 'px' }"
                    >
                        <div
                            class="w-px h-1.5 bg-gray-400 dark:bg-gray-600"
                        ></div>
                        <span class="pl-1 select-none">{{ tick.label }}</span>
                    </div>
                </div>

                <!-- RULER - Y (LEFT) -->
                <div
                    class="absolute left-0 top-0 w-6 bottom-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-10 overflow-hidden border-r border-gray-200 dark:border-gray-800"
                >
                    <div
                        v-for="tick in rulerTicksY"
                        :key="tick.label"
                        class="absolute left-0 text-[10px] text-gray-500 dark:text-gray-400"
                        :style="{ top: tick.position + 'px' }"
                    >
                        <div
                            class="h-px w-1.5 bg-gray-400 dark:bg-gray-600"
                        ></div>
                        <span
                            class="absolute -rotate-90 origin-top-left top-1 left-2.5 whitespace-nowrap select-none"
                            >{{ tick.label }}</span
                        >
                    </div>
                </div>

                <!-- Viewport (Now offset by top-6 and left-6) -->
                <div
                    ref="viewportEl"
                    class="absolute inset-0 top-6 left-6 overflow-hidden"
                    :style="gridStyle"
                >
                    <svg
                        ref="svgCanvas"
                        class="w-full h-full block"
                        :style="{
                            transform: `translate(${viewportTransform.x}px, ${viewportTransform.y}px) scale(${viewportTransform.k})`,
                            transformOrigin: '0 0',
                            cursor:
                                tool === 'pan'
                                    ? 'grab'
                                    : tool === 'select'
                                    ? 'default'
                                    : 'crosshair',
                        }"
                    >
                        <!-- ... a lot of svg content here, no changes inside the svg element ... -->

                        <defs>
                            <pattern
                                id="gridpattern"
                                :width="toPx(gridMeters)"
                                :height="toPx(gridMeters)"
                                patternUnits="userSpaceOnUse"
                            >
                                <path
                                    :d="`M ${toPx(gridMeters)} 0 L 0 0 0 ${toPx(
                                        gridMeters
                                    )}`"
                                    fill="none"
                                    class="stroke-gray-200 dark:stroke-gray-800"
                                    stroke-width="1"
                                />
                            </pattern>
                        </defs>
                        <rect
                            id="bgRect"
                            x="-10000"
                            y="-10000"
                            width="20000"
                            height="20000"
                            fill="url(#gridpattern)"
                            v-if="showGrid"
                        ></rect>
                        <g id="backgroundLayer">
                            <image
                                v-if="bgImage.src"
                                :href="bgImage.src"
                                x="0"
                                y="0"
                                width="2000"
                                height="1200"
                                :opacity="bgImage.opacity"
                            />
                        </g>
                        <g id="itemsLayer">
                            <g
                                v-for="obj in objects"
                                :key="obj.id"
                                :data-id="obj.id"
                                class="floor-item"
                                :transform="
                                    obj.type === 'rect'
                                        ? `translate(${obj.x} ${
                                              obj.y
                                          }) rotate(${obj.rot} ${obj.w / 2} ${
                                              obj.h / 2
                                          })`
                                        : obj.type === 'circle'
                                        ? `translate(${obj.cx} ${obj.cy}) rotate(${obj.rot})`
                                        : ''
                                "
                                :class="{
                                    'opacity-60': obj.locked,
                                    'cursor-move': !obj.locked,
                                    'cursor-not-allowed': obj.locked,
                                    'drop-shadow-md': selected.includes(obj.id),
                                }"
                            >
                                <!-- Rectangle -->
                                <template v-if="obj.type === 'rect'">
                                    <rect
                                        :width="obj.w"
                                        :height="obj.h"
                                        :fill="obj.fill"
                                        stroke-width="1"
                                        :class="{
                                            'stroke-brand-500 stroke-[2.5px]':
                                                selected.includes(obj.id),
                                            'stroke-gray-800 dark:stroke-gray-600':
                                                !selected.includes(obj.id),
                                        }"
                                    />
                                    <text
                                        :x="obj.w / 2"
                                        :y="obj.h / 2"
                                        dominant-baseline="middle"
                                        text-anchor="middle"
                                        class="text-xs pointer-events-none fill-gray-900 dark:fill-gray-100 select-none"
                                    >
                                        {{ obj.meta.name }}
                                    </text>
                                </template>
                                <!-- Circle -->
                                <template v-if="obj.type === 'circle'">
                                    <circle
                                        :r="obj.r"
                                        :fill="obj.fill"
                                        stroke-width="1"
                                        :class="{
                                            'stroke-brand-500 stroke-[2.5px]':
                                                selected.includes(obj.id),
                                            'stroke-gray-800 dark:stroke-gray-600':
                                                !selected.includes(obj.id),
                                        }"
                                    />
                                    <text
                                        dominant-baseline="middle"
                                        text-anchor="middle"
                                        class="text-xs pointer-events-none fill-gray-900 dark:fill-gray-100 select-none"
                                    >
                                        {{ obj.meta.name }}
                                    </text>
                                </template>
                            </g>
                        </g>
                        <g id="uiLayer">
                            <rect
                                v-if="
                                    dragState?.type === 'create' &&
                                    dragState.tool === 'rect'
                                "
                                :x="
                                    Math.min(dragState.start.x, dragState.end.x)
                                "
                                :y="
                                    Math.min(dragState.start.y, dragState.end.y)
                                "
                                :width="
                                    Math.abs(
                                        dragState.end.x - dragState.start.x
                                    )
                                "
                                :height="
                                    Math.abs(
                                        dragState.end.y - dragState.start.y
                                    )
                                "
                                class="fill-brand-500/20 stroke-brand-500/50 pointer-events-none"
                                stroke-width="1"
                                stroke-dasharray="4 3"
                            />
                            <circle
                                v-if="
                                    dragState?.type === 'create' &&
                                    dragState.tool === 'circle'
                                "
                                :cx="dragState.start.x"
                                :cy="dragState.start.y"
                                :r="
                                    Math.hypot(
                                        dragState.end.x - dragState.start.x,
                                        dragState.end.y - dragState.start.y
                                    )
                                "
                                class="fill-amber-500/20 stroke-amber-500/50 pointer-events-none"
                                stroke-width="1"
                                stroke-dasharray="4 3"
                            />
                        </g>
                    </svg>
                </div>

                <!-- Minimap -->
                <div
                    class="absolute bottom-4 right-4 w-52 h-36 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border border-gray-300 dark:border-gray-700 rounded-lg shadow-md overflow-hidden z-10 hidden lg:block minimap"
                >
                    <svg
                        ref="minimapSvgEl"
                        :viewBox="miniMapViewBox"
                        class="w-full h-full cursor-pointer"
                        @mousedown.prevent="onMinimapMouseDown"
                        @mousemove.prevent="onMinimapMouseMove"
                    >
                        <!-- Rendered objects (now non-interactive) -->
                        <g class="pointer-events-none">
                            <g v-for="item in miniMapItems" :key="item.id">
                                <rect
                                    v-if="item.type === 'rect'"
                                    :x="item.x"
                                    :y="item.y"
                                    :width="item.w"
                                    :height="item.h"
                                    class="fill-brand-500/30 stroke-brand-500/50"
                                    stroke-width="0.5"
                                />
                                <circle
                                    v-else-if="item.type === 'circle'"
                                    :cx="item.cx"
                                    :cy="item.cy"
                                    :r="item.r"
                                    class="fill-amber-500/30 stroke-amber-500/50"
                                    stroke-width="0.5"
                                />
                            </g>
                        </g>

                        <!-- Viewport Rectangle (now non-interactive) -->
                        <rect
                            :x="miniMapViewFinder.x"
                            :y="miniMapViewFinder.y"
                            :width="miniMapViewFinder.w"
                            :height="miniMapViewFinder.h"
                            class="fill-red-500/20 stroke-red-500 pointer-events-none"
                            stroke-width="1.5"
                        />
                    </svg>
                </div>
            </div>
        </main>

        <!-- Right Inspector (REVISED FOR OVERLAPPING BEHAVIOR) -->
        <aside
            v-if="inspectorObject"
            class="w-[280px] flex-shrink-0 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-3 overflow-y-auto hidden lg:block inspector"
        >
            <h3
                class="px-1 text-sm font-semibold text-gray-500 dark:text-gray-400"
            >
                Inspector
            </h3>

            <div
                v-if="!inspectorObject"
                class="mt-4 text-center text-xs text-gray-400 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg"
            >
                No item selected.
            </div>

            <div v-else class="mt-2 space-y-3">
                <!-- ... All the inspector form content remains exactly the same ... -->
                <div>
                    <label class="block mb-1 text-xs text-gray-500">Name</label>
                    <input
                        v-model="inspectorProps.name"
                        @input="applyProps"
                        type="text"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Exhibitor</label
                    >
                    <input
                        v-model="inspectorProps.exhibitor"
                        @input="applyProps"
                        type="text"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Booth #</label
                    >
                    <input
                        v-model="inspectorProps.number"
                        @input="applyProps"
                        type="text"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block mb-1 text-xs text-gray-500"
                            >Width (m)</label
                        >
                        <input
                            v-model.number="inspectorProps.w"
                            @change="applyProps"
                            type="number"
                            step="0.1"
                            class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        />
                    </div>
                    <div>
                        <label class="block mb-1 text-xs text-gray-500"
                            >Height (m)</label
                        >
                        <input
                            v-model.number="inspectorProps.h"
                            @change="applyProps"
                            type="number"
                            step="0.1"
                            class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 items-end">
                    <div>
                        <label class="block mb-1 text-xs text-gray-500"
                            >Rotation (deg)</label
                        >
                        <input
                            v-model.number="inspectorProps.rot"
                            @input="applyProps"
                            type="number"
                            step="1"
                            class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        />
                    </div>
                    <div>
                        <label class="block mb-1 text-xs text-gray-500"
                            >Color</label
                        >
                        <input
                            v-model="inspectorProps.color"
                            type="color"
                            class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-1 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        />
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500"
                        >Notes</label
                    >
                    <textarea
                        v-model="inspectorProps.notes"
                        @input="applyProps"
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    ></textarea>
                </div>
                <div class="flex gap-2 pt-2">
                    <button
                        @click="applyProps"
                        class="hover:bg-brand-600 bg-brand-500 flex-1 rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                    >
                        Apply
                    </button>
                    <button
                        @click="duplicateSelected"
                        class="shadow-sm flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                    >
                        Duplicate
                    </button>
                </div>

                <!-- ADD THIS BLOCK FOR Z-ORDER BUTTONS -->
                <div class="flex gap-2">
                    <button
                        @click="bringForward"
                        class="shadow-sm flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                    >
                        Bring Forward
                    </button>
                    <button
                        @click="sendBack"
                        class="shadow-sm flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                    >
                        Send Back
                    </button>
                    <button
                        @click="toggleLock"
                        class="shadow-sm flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                    >
                        {{ inspectorObject.locked ? "Unlock" : "Lock" }}
                    </button>
                </div>

                <div class="h-px bg-gray-200 dark:border-gray-800 my-4"></div>
                <div class="flex gap-2">
                    <button
                        @click="deleteSelected"
                        class="shadow-sm flex-1 rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03] text-red-600 dark:text-red-500"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </aside>
    </div>
</template>
