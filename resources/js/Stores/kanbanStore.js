import { defineStore } from "pinia";
import { router } from "@inertiajs/vue3";

export const useKanbanStore = defineStore("kanban", {
    state: () => ({
        columns: [
            { id: 1, title: "To Do", tasks: [] },
            { id: 2, title: "In Progress", tasks: [] },
            { id: 3, title: "Completed", tasks: [] },
        ],
        users: [],
        tags: [],
    }),

    getters: {
        getTask: (state) => (taskId) => {
            for (const column of state.columns) {
                const task = column.tasks.find((t) => t.id === taskId);
                if (task) return task;
            }
            return null;
        },
        toDoCount: (state) =>
            state.columns.find((c) => c.id === 1)?.tasks.length || 0,
        inProgressCount: (state) =>
            state.columns.find((c) => c.id === 2)?.tasks.length || 0,
        completedCount: (state) =>
            state.columns.find((c) => c.id === 3)?.tasks.length || 0,
        allTasksCount: (state) =>
            state.columns.reduce((total, col) => total + col.tasks.length, 0),
    },

    actions: {
        // 💡 NEW ACTION: Set columns from an external source (like backend data)
        setColumns(newColumns) {
            // Overwrite the hardcoded columns with the new data
            // We ensure each column has a tasks array, even if empty
            this.columns = newColumns.map((col) => ({
                ...col,
                tasks: col.tasks || [],
            }));
        },

        setMasterData({ users, tags }) {
            this.users = users;
            this.tags = tags;
        },

        setTasks(tasks) {
            this.columns.forEach((col) => (col.tasks = []));
            tasks.forEach((task) => {
                const column = this.columns.find(
                    (col) => col.id === task.column_id
                );
                if (column) {
                    column.tasks.push(task);
                }
            });
        },

        addTask(task) {
            const column = this.columns.find((c) => c.id === task.column_id);
            if (column) {
                column.tasks.push(task);
            }
        },

        updateTask(updatedTask) {
            // Find and remove the old task from its original column
            for (const column of this.columns) {
                const index = column.tasks.findIndex(
                    (t) => t.id === updatedTask.id
                );
                if (index !== -1) {
                    column.tasks.splice(index, 1);
                    break;
                }
            }
            // Add the updated task to its new column for a seamless move
            this.addTask(updatedTask);
        },

        moveTask(event) {
            if (!event.added && !event.moved) return;

            const taskId = event.item.dataset.taskId;
            const targetColumnId = event.to.dataset.columnId;
            const orderedTaskIds = Array.from(event.to.children).map(
                (el) => el.dataset.taskId
            );

            if (!taskId || !targetColumnId) return;

            router.patch(
                route("tasks.move"),
                {
                    taskId: taskId,
                    targetColumnId: targetColumnId,
                    orderedIds: orderedTaskIds,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onError: () => {
                        alert("Failed to move task! Please refresh.");
                    },
                }
            );
        },
    },
});
