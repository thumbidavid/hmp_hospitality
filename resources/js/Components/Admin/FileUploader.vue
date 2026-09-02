<script setup>
import { ref } from 'vue'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'
import axios from 'axios'

const FilePond = vueFilePond(FilePondPluginImagePreview)

const props = defineProps({
    modelValue: [File, Array, null],
    initialFileUrl: { type: String, default: null },
    options: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])
const files = ref([])
const isUploading = ref(false) // Add a lock

const handleUpdateFiles = async (fileItems) => {
    // 1. If we are already uploading, or the files array is empty, just update local state
    if (isUploading.value) return

    files.value = fileItems

    // 2. Only proceed if there is a file that hasn't been uploaded yet
    const fileToUpload = fileItems.find(item => item.file && item.status === 2) // status 2 is usually 'added' in FilePond

    if (fileToUpload) {
        isUploading.value = true // Lock

        try {
            const formData = new FormData()
            formData.append('file', fileToUpload.file)

            const response = await axios.post(route('app.admin.media.store'), formData)

            // Emit the ID returned by the MediaController
            emit('update:modelValue', response.data.id)
        } catch (error) {
            console.error('Upload failed', error)
        } finally {
            isUploading.value = false // Unlock
        }
    }
}
</script>

<template>
    <div class="file-uploader">
        <div
            class="custom-filepond hover:border-brand-500 dark:hover:border-brand-500 rounded-xl border-dashed border-gray-300 bg-gray-50 p-7 lg:p-10 dark:border-gray-700 dark:bg-gray-900">
            <FilePond :files="files" @updatefiles="handleUpdateFiles" :allow-multiple="options.allowMultiple || false"
                :max-files="options.maxFiles || 1" :accepted-file-types="options.acceptedFileTypes || [
                    'image/png',
                    'image/jpeg',
                    'image/webp',
                    'image/svg+xml',
                ]
                    " :label-idle="`
                <div class='flex flex-col items-center'>
                    <div class='mb-[22px] flex justify-center'>
                        <div
                            class='flex h-[68px] w-[68px] items-center justify-center rounded-full bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-400'
                        >
                            <svg
                                class='fill-current'
                                width='29'
                                height='28'
                                viewBox='0 0 29 28'
                                xmlns='http://www.w3.org/2000/svg'
                            >
                                <path
                                    d='M14.5019 3.91699C14.2852 3.91699 14.0899 4.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z'
                                />
                            </svg>
                        </div>
                    </div>

                    <h4 class='mb-3 font-semibold text-gray-800 text-theme-xl dark:text-white/90'>
                        Drag & Drop Files Here
                    </h4>



                    <span class='font-medium underline cursor-pointer text-theme-sm text-brand-500'>
                        Browse File
                    </span>
                </div>
                `" :credits="false" />
        </div>
    </div>
</template>

<style>
.custom-filepond .filepond--panel-root {
    background: transparent;
    border: none;
}

.custom-filepond .filepond--drop-label {
    color: inherit;
}

.custom-filepond .filepond--root {
    margin-bottom: 0;
    font-family: inherit;
}

.custom-filepond .filepond--drip {
    opacity: 0.08;
}

.custom-filepond .filepond--item-panel {
    background-color: #465fff;
}

.custom-filepond .filepond--file {
    color: white;
}
</style>
