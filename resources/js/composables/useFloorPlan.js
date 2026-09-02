import { ref, reactive, computed, watchEffect } from 'vue';

// --- UTILITIES ---
const uid = (prefix = 'i') => `${prefix}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 7)}`;

export function useFloorPlan() {
    // --- CORE STATE ---
    const objects = ref([]);
    const selectedIds = ref([]);
    const tool = ref('select'); // 'select', 'rect', 'circle', 'text', etc.
    const viewportTransform = reactive({ x: 0, y: 0, k: 1 });
    const dragState = ref(null); // Manages all interactions: move, resize, rotate, create, box-select

    // --- ENVIRONMENT STATE ---
    const isSnap = ref(true);
    const showGrid = ref(true);
    const pxPerMeter = ref(60);
    const gridMeters = ref(1);
    const backgroundImage = reactive({ src: null, opacity: 0.5, width: 2000, height: 1500 });
    const autoArrangeSettings = reactive({ rows: 3, cols: 5 });

    // --- HISTORY STATE ---
    const history = ref([]);
    const historyIndex = ref(-1);

    // --- COMPUTED PROPERTIES ---
    const gridPatternSize = computed(() => Number(gridMeters.value) * Number(pxPerMeter.value));
    const selectedObject = computed(() => (selectedIds.value.length === 1) ? objects.value.find(o => o.id === selectedIds.value[0]) : null);
    const isDragging = computed(() => !!dragState.value);

    // --- COLLISION DETECTION ---
    watchEffect(() => {
        objects.value.forEach(obj => obj.isColliding = false);
        for (let i = 0; i < objects.value.length; i++) {
            for (let j = i + 1; j < objects.value.length; j++) {
                const o1 = objects.value[i]; const o2 = objects.value[j];
                if (!o1.isLocked && !o2.isLocked && o1.x < o2.x + o2.w && o1.x + o1.w > o2.x && o1.y < o2.y + o2.h && o1.y + o1.h > o2.y) {
                    o1.isColliding = o2.isColliding = true;
                }
            }
        }
    });

    // --- HELPERS ---
    const getObjectById = (id) => objects.value.find(o => o.id === id);
    const snapToGrid = (val) => {
        if (!isSnap.value) return val;
        const gridPx = gridPatternSize.value;
        return gridPx > 0 ? Math.round(val / gridPx) * gridPx : val;
    };

    // --- HISTORY (UNDO/REDO) ---
    const pushHistory = (label) => {
        const snapshot = JSON.stringify(objects.value);
        if (history.debounceTimer) clearTimeout(history.debounceTimer);
        history.debounceTimer = setTimeout(() => {
            if (history.value[historyIndex.value]?.snapshot === snapshot) return;
            const newHistory = history.value.slice(0, historyIndex.value + 1);
            newHistory.push({ label, snapshot });
            history.value = newHistory;
            historyIndex.value++;
        }, 300);
    };
    const undo = () => { if (historyIndex.value > 0) { historyIndex.value--; objects.value = JSON.parse(history.value[historyIndex.value].snapshot); deselectAll(); }};
    const redo = () => { if (historyIndex.value < history.value.length - 1) { historyIndex.value++; objects.value = JSON.parse(history.value[historyIndex.value].snapshot); deselectAll(); }};

    // --- MODEL CREATION ---
    const createObjectModel = (type, props) => {
        const base = { id: uid(type), type, rot: 0, isLocked: false, isColliding: false, meta: {}, ...props };
        if (type === 'rect') return { ...base, fill: '#60a5fa', meta: { name: 'Booth' } };
        if (type === 'circle') return { ...base, fill: '#f59e0b', meta: { name: 'Feature' } };
        if (type === 'text') return { ...base, fill: '#334155', h: props.meta.fontSize, meta: { content: 'Label', fontSize: 24 } };
        return base;
    };

    // --- SELECTION ---
    const selectObject = (objectId, isMultiSelect = false) => {
        const obj = getObjectById(objectId);
        if (obj?.isLocked) return;
        if (isMultiSelect) {
            selectedIds.value = selectedIds.value.includes(objectId) ? selectedIds.value.filter(id => id !== objectId) : [...selectedIds.value, objectId];
        } else {
            selectedIds.value = [objectId];
        }
    };
    const deselectAll = () => { selectedIds.value = []; };

    // --- DRAG & DROP LOGIC ---
    const startDrag = (type, startPoint, payload = {}) => {
        let originals = new Map();
        if (type === 'move') selectedIds.value.forEach(id => originals.set(id, { ...getObjectById(id) }));
        else if (type === 'resize' || type === 'rotate') originals.set(selectedObject.value.id, { ...selectedObject.value });
        else if (type === 'pan') originals.set('viewport', { ...viewportTransform });

        dragState.value = { type, startPoint, originals, ...payload };

        if (type === 'create') dragState.value.ghost = { x: startPoint.x, y: startPoint.y, width: 0, height: 0 };
        if (type === 'box-select') dragState.value.endPoint = { ...startPoint };
    };

    const drag = (currentPoint, payload = {}) => {
        if (!dragState.value) return;
        const { type, originals } = dragState.value;

        // Special handling for Pan, which works in screen space
        if (type === 'pan') {
            const dx = currentPoint.x - dragState.value.startPoint.x;
            const dy = currentPoint.y - dragState.value.startPoint.y;
            const originalViewport = originals.get('viewport');

            viewportTransform.x = Math.min(0, originalViewport.x + dx);
            viewportTransform.y = Math.min(0, originalViewport.y + dy);
            return;
        }

        // Special handling for Zoom
        if (type === 'zoom') {
            const { factor, point } = payload;
            const oldScale = viewportTransform.k;
            const newScale = Math.max(0.1, Math.min(oldScale * factor, 5));

            viewportTransform.x = point.x - (point.x - viewportTransform.x) * (newScale / oldScale);
            viewportTransform.y = point.y - (point.y - viewportTransform.y) * (newScale / oldScale);

            viewportTransform.k = newScale;
            viewportTransform.x = Math.min(0, viewportTransform.x);
            viewportTransform.y = Math.min(0, viewportTransform.y);
            return;
        }

        // All other actions work in SVG space
        const dx = currentPoint.x - dragState.value.startPoint.x;
        const dy = currentPoint.y - dragState.value.startPoint.y;

        if (type === 'move') {
            originals.forEach((original, id) => { const obj = getObjectById(id); if (obj) { obj.x = original.x + dx; obj.y = original.y + dy; }});
        } else if (type === 'resize') {
            const obj = selectedObject.value; const orig = originals.get(obj.id);
            if (dragState.value.handle === 'se') { obj.w = Math.max(20, orig.w + dx); obj.h = Math.max(20, orig.h + dy); }
            // ... other resize handles
        } else if (type === 'rotate') {
            const obj = selectedObject.value; const orig = originals.get(obj.id);
            const centerX = orig.x + orig.w / 2; const centerY = orig.y + orig.h / 2;
            const startAngle = Math.atan2(dragState.value.startPoint.y - centerY, dragState.value.startPoint.x - centerX);
            const currentAngle = Math.atan2(currentPoint.y - centerY, currentPoint.x - centerX);
            const angleDelta = (currentAngle - startAngle) * 180 / Math.PI;
            obj.rot = (orig.rot + angleDelta + 360) % 360;
        } else if (type === 'create') {
            dragState.value.ghost = { x: Math.min(dragState.value.startPoint.x, currentPoint.x), y: Math.min(dragState.value.startPoint.y, currentPoint.y), width: Math.abs(dx), height: Math.abs(dy) };
        } else if (type === 'box-select') {
            dragState.value.endPoint = currentPoint;
            const box = { x: Math.min(dragState.value.startPoint.x, currentPoint.x), y: Math.min(dragState.value.startPoint.y, currentPoint.y), width: Math.abs(dx), height: Math.abs(dy) };
            const newSelected = [];
            objects.value.forEach(o => {
                if (!o.isLocked && o.x < box.x + box.width && o.x + o.w > box.x && o.y < box.y + box.height && o.y + o.h > box.y) {
                    newSelected.push(o.id);
                }
            });
            selectedIds.value = newSelected;
        }
    };

    const endDrag = () => {
        if (!dragState.value) return;
        const { type, ghost } = dragState.value;
        if (type === 'create' && ghost.width > 10 && ghost.height > 10) {
            const newObj = createObjectModel(dragState.value.tool, { x: snapToGrid(ghost.x), y: snapToGrid(ghost.y), w: snapToGrid(ghost.width), h: snapToGrid(ghost.height), meta: {name: 'New ' + dragState.value.tool} });
            objects.value.push(newObj); selectObject(newObj.id); pushHistory('Create');
        } else if (['move', 'resize', 'rotate'].includes(type)) {
            objects.value.forEach(o => { if(selectedIds.value.includes(o.id)) { o.x = snapToGrid(o.x); o.y = snapToGrid(o.y); o.w = snapToGrid(o.w); o.h = snapToGrid(o.h); }});
            pushHistory('Transform');
        }
        dragState.value = null;
    };

    // --- OBJECT MANIPULATION ---
    const deleteSelected = () => { if (selectedIds.value.length) { objects.value = objects.value.filter(o => !selectedIds.value.includes(o.id)); deselectAll(); pushHistory('Delete'); }};
    const toggleLock = () => { if (selectedObject.value) { selectedObject.value.isLocked = !selectedObject.value.isLocked; pushHistory('Lock/Unlock'); }};
    const bringForward = () => { if (!selectedObject.value) return; const i = objects.value.findIndex(o=>o.id===selectedObject.value.id); if (i<objects.value.length-1) { const [item]=objects.value.splice(i,1); objects.value.push(item); pushHistory('Layer Change'); }};
    const sendBackward = () => { if (!selectedObject.value) return; const i = objects.value.findIndex(o=>o.id===selectedObject.value.id); if (i>0) { const [item]=objects.value.splice(i,1); objects.value.unshift(item); pushHistory('Layer Change'); }};
    const duplicateSelected = () => { if (!selectedObject.value) return; const o = selectedObject.value; const n = {...JSON.parse(JSON.stringify(o)), id: uid(o.type), x: o.x+20, y: o.y+20}; objects.value.push(n); selectedIds.value=[n.id]; pushHistory('Duplicate'); };

    // --- DATA & AUTOMATION ---
    const saveToJson = () => { const d = {objects: objects.value, pxPerMeter: pxPerMeter.value, gridMeters: gridMeters.value}; const b = new Blob([JSON.stringify(d, null, 2)], {type: 'application/json'}); const a = document.createElement('a'); a.href=URL.createObjectURL(b); a.download = 'floorplan.json'; a.click(); URL.revokeObjectURL(a.href); };
    const loadFromJson = (e) => { const f=e.target.files[0]; if(!f)return; const r=new FileReader(); r.onload=e=>{try{const d=JSON.parse(e.target.result); objects.value=d.objects||[];pxPerMeter.value=d.pxPerMeter||60;gridMeters.value=d.gridMeters||1;deselectAll();pushHistory('Load');}catch(err){alert('Error parsing JSON.');}}; r.readAsText(f); e.target.value=''; };
    const loadBackgroundImage = (e) => { const f=e.target.files[0]; if(f){const r=new FileReader(); r.onload=e=>backgroundImage.src=e.target.result; r.readAsDataURL(f);} };
    const autoArrange = () => { if(!confirm('Replace layout?')) return; const { rows, cols } = autoArrangeSettings; const [w,h,g,sx,sy]=[120,120,60,60,60]; const n=[]; for(let r=0;r<rows;r++){for(let c=0;c<cols;c++){n.push(createObjectModel('rect',{x:sx+c*(w+g),y:sy+r*(h+g),w,h,meta:{name:`B-${r+1}-${c+1}`}}));}} objects.value=n; deselectAll(); pushHistory('Auto-Arrange'); };
    const clearAll = () => { if(confirm('Clear canvas?')){objects.value=[];deselectAll();pushHistory('Clear');}};
    const addInitialObjects = () => { objects.value.push(createObjectModel('rect',{x:240,y:180,w:180,h:120,meta:{name:'Booth A'}})); pushHistory('Initial State'); };

    // --- EXPOSE PUBLIC API ---
    return {
        // State
        objects, selectedIds, tool, viewportTransform, dragState, isSnap, showGrid, pxPerMeter, gridMeters, backgroundImage, autoArrangeSettings, history, historyIndex,
        // Computed
        selectedObject, isDragging,
        // Methods
        selectObject, deselectAll, addInitialObjects, startDrag, drag, endDrag, undo, redo, setTool: (t) => { tool.value = t; },
        deleteSelected, toggleLock, bringForward, sendBackward, duplicateSelected,
        saveToJson, loadFromJson, loadBackgroundImage, autoArrange, clearAll, getObjectById,
    };
}
