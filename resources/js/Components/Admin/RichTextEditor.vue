<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import { StarterKit } from '@tiptap/starter-kit'
import { Color } from '@tiptap/extension-color'
import { TextStyle } from '@tiptap/extension-text-style'
import { ListItem } from '@tiptap/extension-list-item'
import { Link } from '@tiptap/extension-link'
import { watch } from 'vue'

const props = defineProps({
    modelValue: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            // History is part of StarterKit, handling undo/redo for content changes
            history: true,
        }),
        TextStyle,
        ListItem,
        Color.configure({ types: [TextStyle.name, ListItem.name] }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-blue-600 underline cursor-pointer',
            },
        }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML())
    },
})

// Sync from parent
watch(() => props.modelValue, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value, false)
    }
})

// Link Function
const setLink = () => {
    const previousUrl = editor.value.getAttributes('link').href
    const url = window.prompt('URL', previousUrl)
    if (url === null) return // cancelled
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
        return
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

// Styling Helper
const btnClass = (isActive = false) => [
    'px-2 py-1 text-xs rounded border transition-all',
    isActive
        ? 'bg-blue-600 text-white border-blue-700 shadow-sm'
        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700'
].join(' ')
</script>

<template>
    <div v-if="editor" class="border border-gray-300 rounded-lg overflow-hidden dark:border-gray-700 shadow-sm">
        <!-- Toolbar -->
        <div class="flex flex-wrap gap-1 p-2 bg-gray-50 border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700">
            <button @click.prevent="editor.chain().focus().toggleBold().run()"
                :class="btnClass(editor.isActive('bold'))" class="font-bold">B</button>
            <button @click.prevent="editor.chain().focus().toggleItalic().run()"
                :class="btnClass(editor.isActive('italic'))" class="italic">I</button>
            <button @click.prevent="editor.chain().focus().toggleStrike().run()"
                :class="btnClass(editor.isActive('strike'))" class="line-through">S</button>
            <button @click.prevent="editor.chain().focus().toggleCode().run()"
                :class="btnClass(editor.isActive('code'))">Code</button>

            <div class="w-px h-6 bg-gray-300 mx-1"></div>

            <button @click.prevent="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                :class="btnClass(editor.isActive('heading', { level: 1 }))">H1</button>
            <button @click.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="btnClass(editor.isActive('heading', { level: 2 }))">H2</button>
            <button @click.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="btnClass(editor.isActive('heading', { level: 3 }))">H3</button>

            <div class="w-px h-6 bg-gray-300 mx-1"></div>

            <button @click.prevent="editor.chain().focus().toggleBulletList().run()"
                :class="btnClass(editor.isActive('bulletList'))">Bullet List</button>
            <button @click.prevent="editor.chain().focus().toggleOrderedList().run()"
                :class="btnClass(editor.isActive('orderedList'))">Ordered List</button>
            <button @click.prevent="editor.chain().focus().toggleBlockquote().run()"
                :class="btnClass(editor.isActive('blockquote'))">Quote</button>

            <div class="w-px h-6 bg-gray-300 mx-1"></div>

            <button @click.prevent="setLink" :class="btnClass(editor.isActive('link'))">Link</button>
            <button @click.prevent="editor.chain().focus().unsetLink().run()" :disabled="!editor.isActive('link')"
                :class="btnClass()">Unlink</button>

            <div class="w-px h-6 bg-gray-300 mx-1"></div>

            <button @click.prevent="editor.chain().focus().undo().run()" :class="btnClass()">Undo</button>
            <button @click.prevent="editor.chain().focus().redo().run()" :class="btnClass()">Redo</button>
            <button @click.prevent="editor.chain().focus().setColor('#958DF1').run()"
                :class="btnClass(editor.isActive('textStyle', { color: '#958DF1' }))"
                class="!text-[#958DF1]">Purple</button>
        </div>

        <!-- Editor Area -->
        <editor-content :editor="editor" class="editor-shell" />
    </div>
</template>

<style>
/* 1. FIXING THE HEADINGS AND LISTS */
/* We target .ProseMirror specifically to override Tailwind's reset */

.editor-shell .ProseMirror {
    outline: none;
    padding: 1.5rem;
    min-height: 300px;
}

/* Headings: Bold and Big */
.ProseMirror h1 {
    font-size: 2.25rem;
    font-weight: 800;
    line-height: 1.2;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.ProseMirror h2 {
    font-size: 1.875rem;
    font-weight: 700;
    line-height: 1.3;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.ProseMirror h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

/* Lists: Visible Bullets and Numbers */
.ProseMirror ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin: 1rem 0;
}

.ProseMirror ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin: 1rem 0;
}

.ProseMirror li p {
    margin: 0;
}

/* Links */
.ProseMirror a {
    color: #2563eb;
    text-decoration: underline;
    cursor: pointer;
}

/* Other Elements */
.ProseMirror blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    font-style: italic;
    color: #4b5563;
    margin: 1rem 0;
}

.ProseMirror code {
    background-color: #f3f4f6;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.dark .ProseMirror code {
    background-color: #374151;
}

.ProseMirror p.is-editor-empty:first-child::before {
    content: 'Start typing here...';
    color: #adb5bd;
    float: left;
    pointer-events: none;
    height: 0;
}
</style>
