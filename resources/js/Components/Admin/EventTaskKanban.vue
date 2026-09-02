<script setup>
import { onMounted, watch } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import draggable from "vuedraggable";
import { useKanbanStore } from "@/Stores/kanbanStore";
import { useResourceCRUD } from "@/composables/useResourceCRUD";
import { router } from "@inertiajs/vue3";

// --- PROPS ---
const props = defineProps({
    tasks: { type: Array, default: () => [] },
    eventId: { type: Number, required: true },
    taskStatuses: { type: Array, default: () => [] },
});

// --- STORE ---
const kanbanStore = useKanbanStore();

// --- INITIALIZE DATA ---
// Helper function to map tasks from DB format to Store format
const mapTasksForStore = (tasks) => {
    return tasks.map((task) => ({
        ...task,
        // 💡 FIX: Map status_id (DB key) to column_id (Store key)
        column_id: task.status_id,
    }));
};

onMounted(() => {
    // 💡 FIX 1: This call is now valid. Set the store's columns from the backend data
    if (props.taskStatuses.length > 0) {
        kanbanStore.setColumns(
            props.taskStatuses.map((s) => ({
                id: s.id,
                title: s.name, // Use 'name' from the taskStatuses prop
                tasks: [],
            }))
        );
    }

    // 💡 FIX 2: Use the mapped data for the store
    kanbanStore.setTasks(mapTasksForStore(props.tasks));
});

watch(
    () => props.tasks,
    (newTasks) => {
        // 💡 FIX 3: Apply the same mapping on watch updates
        kanbanStore.setTasks(mapTasksForStore(newTasks));
    }
);

// --- CRUD CONFIGURATION ---
const getTaskFieldConfiguration = () => [
    [
        {
            name: "name",
            label: "Task Name",
            component: "TextInput",
            props: { placeholder: "e.g., Finalize guest speaker" },
        },
    ],
    [
        {
            name: "status_id",
            label: "Status",
            component: "SelectInput",
            props: {
                // 💡 FIX 5: Update props for SelectInput to use correct keys
                options: kanbanStore.columns.map((c) => ({
                    value: c.id,
                    label: c.title,
                })),
                optionLabel: "label",
                optionValue: "value",
            },
        },
    ],
];

const crud = useResourceCRUD({
    resourceName: "Task",
    description: "Fill in the details for the event task.",
    entityName: "task",
    routeNames: {
        store: "client.events.tasks.store",
        update: "client.tasks.update",
        destroy: "client.tasks.destroy",
    },

    // ✅ This fixes the Ziggy error
    routeParams: () => ({ event: props.eventId }),

    initialFormState: () => ({
        id: null,
        name: "",
        status_id: 1, // Default to 'To Do'
    }),

    fieldsConfig: getTaskFieldConfiguration(),

    onSuccess: () => {
        router.reload({ only: ["event"] });
    },
});

// --- MODAL + ACTIONS ---
const openCreateTaskModal = () => {
    crud.openCreateModal({ event_id: props.eventId });
};

const handleTaskMove = (event) => {
    // 💡 event.item is the DOM element dragged. Its dataset holds the ID.
    const taskId = event.item.dataset.taskId;

    // 💡 event.to is the DOM element dropped into. Its dataset holds the ID.
    const newColumnId = event.to.dataset.columnId;

    // Add a console log to verify the IDs are found
    console.log(`DEBUG: TaskID: ${taskId}, New ColumnID: ${newColumnId}`);

    if (taskId && newColumnId) {
        const newStatusId = parseInt(newColumnId);
        const currentTaskId = parseInt(taskId);

        // 1. Optimistic Update (Use the correct, existing action)
        const task = kanbanStore.getTask(currentTaskId);

        if (task && task.column_id !== newStatusId) {
            // Call Pinia to update the local state immediately
            kanbanStore.updateTask({
                ...task,
                column_id: newStatusId, // Local store key
                status_id: newStatusId, // Local data for form editing
            });

            // 2. Update backend
            router.put(
                route("client.tasks.update", { task: currentTaskId }), // Use parsed ID
                { status_id: newStatusId }, // Send correct payload
                {
                    preserveState: true,
                    preserveScroll: true,
                    // Handle server errors
                    onError: (errors) => {
                        console.error("Task status update failed:", errors);
                        alert("Task update failed! Check console for errors.");
                        router.reload({ only: ["event"] }); // Force a reload to revert the optimistic update
                    },
                }
            );
        }
    }
};
</script>

<template>
    <div
        class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
    >
        <!-- Header -->
        <div
            class="flex flex-wrap items-center justify-between gap-4 px-4 py-5 xl:px-6"
        >
            <h3 class="text-lg font-medium">Task Board</h3>
            <PrimaryButton @click="openCreateTaskModal">
                Add New Task
            </PrimaryButton>
        </div>

        <!-- Kanban Columns -->
        <div
            class="grid grid-cols-1 border-t border-gray-200 dark:border-gray-800 md:grid-cols-2 xl:grid-cols-3"
        >
            <div
                v-for="column in kanbanStore.columns"
                :key="column.id"
                class="flex flex-col gap-5 p-4 border-gray-200 dark:border-gray-800 xl:p-6 [&:not(:last-child)]:border-r"
            >
                <h3
                    class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white"
                >
                    {{ column.title }}
                    <span
                        class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                    >
                        {{ column.tasks.length }}
                    </span>
                </h3>

                <draggable
                    :list="column.tasks"
                    group="tasks"
                    item-key="id"
                    class="flex flex-col gap-5 min-h-[200px]"
                    tag="div"
                    :data-column-id="column.id"
                    @end="handleTaskMove"
                >
                    <template #item="{ element: task }">
                        <div
                            @click="crud.openEditModal(task)"
                            :data-task-id="task.id"
                            class="cursor-pointer rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-800 hover:shadow-md transition-shadow"
                        >
                            <h4
                                class="mb-2 text-base text-gray-800 dark:text-white"
                            >
                                {{ task.name }}
                            </h4>
                        </div>
                    </template>
                </draggable>
            </div>
        </div>
    </div>
</template>
