<template>
    <div class="vue-file-upload-energeek">
        <div ref="toastNotification" class="toast-file-upload">Ini adalah toast notification!</div>
        <div @click="fileInputRef?.click()" :style="{
            border: '2px dashed #009ef7',
            borderRadius: '8px',
            padding: '20px',
            textAlign: 'center',
            cursor: 'pointer',
            backgroundColor: isDragging ? '#f1faff' : '#f1faff',
        }" @dragenter.prevent="handleDragEnter" @dragleave.prevent="handleDragLeave"
            @dragover.prevent="handleDragEnter" @drop.prevent="handleDrop">
            <input type="file" ref="fileInputRef" style="display: none" @change="handleFileChange"
                :accept="props.allowedExtensions.join(', ')" multiple>

            <div v-if="selectedFiles.length > 0" style="margin-top: 20px">
                <div class="file-upload-list-file">
                    <div v-for="(file, index) in selectedFiles" :key="index" class="file-upload-preview-file"
                        style="margin-bottom: 10px">
                        <img v-if="file.type.startsWith('image/')" :src="displayImage(file)" :alt="file.name"
                            style="max-width: 100%; height: 50px; object-fit: cover; margin-bottom: 5px">
                        <div class="file-upload-preview-size">{{ formatFileSize(file.size) }}</div>
                        <div class="file-upload-preview-name">{{ file.name }}</div>
                        <button class="remove-file-upload-button" @click="handleRemoveFile(file, $event)"
                            style="margin-left: 10px; color: red">
                            <svg width="10px" height="10px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                    fill="#0F1729" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div v-else>
                <label style="display: block; cursor: pointer;text-align: left;">
                    <div class="d-flex align-items-center">
                        <div>
                            <svg width="43" height="43" viewBox="0 0 43 43" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3"
                                    d="M10.4941 3.58301H24.6112C25.2466 3.58301 25.8615 3.80819 26.3467 4.21858L34.8815 11.4376C35.4852 11.9482 35.8334 12.6988 35.8334 13.4895V35.9823C35.8334 39.1904 35.7968 39.4163 32.506 39.4163H10.4941C7.20338 39.4163 7.16675 39.1904 7.16675 35.9823V7.01704C7.16675 3.80898 7.20338 3.58301 10.4941 3.58301Z"
                                    fill="#3E97FF" />
                                <path fillRule="evenodd" clipRule="evenodd"
                                    d="M16.0377 24.7523H19.543V28.3494C19.543 28.8442 19.9441 29.2453 20.4389 29.2453H22.2723C22.767 29.2453 23.1681 28.8442 23.1681 28.3494V24.7523H26.6735C27.1682 24.7523 27.5693 24.3513 27.5693 23.8565C27.5693 23.6445 27.4941 23.4394 27.3572 23.2777L22.0393 16.9967C21.7196 16.6191 21.1543 16.5721 20.7767 16.8918C20.7389 16.9238 20.7039 16.9589 20.6719 16.9967L15.354 23.2777C15.0343 23.6552 15.0812 24.2205 15.4588 24.5402C15.6206 24.6772 15.8257 24.7523 16.0377 24.7523Z"
                                    fill="#3E97FF" />
                            </svg>
                        </div>
                        <div style="margin-left: 15px;">
                            <div style="font-weight: bold; color: #333; font-size: 17px">{{ props.title }}</div>
                            <div style="color: #666; font-size: 14px">{{ props.description }}</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {
    ref
} from 'vue'

interface FileUploadProps {
    allowedExtensions: string[];
    title: string,
    description: string,
    maxSizeMegabyte: number;
    maxFiles: number,
    selectedFiles: File[]
}

const props = defineProps<FileUploadProps>();
const emit = defineEmits(['update:selectedFiles'])

const isDragging = ref<boolean>(false);
const fileInputRef = ref<HTMLInputElement | null>(null);
const toastNotification = ref<HTMLDivElement | null>(null);

const handleFiles = (files: FileList) => {
    const validFiles = [];
    let errorMessage = '';

    for (let i = 0; i < files.length; i++) {
        const extension = files[i].name.split('.').pop()?.toLowerCase() || '';
        const ext = props.allowedExtensions.map((ext) => ext.replace(/^\./, ''));
        if (!ext.includes(extension)) {
            errorMessage = `File type not allowed. Allowed types: ${props.allowedExtensions.join(', ')}`;
            continue;
        }
        const fileSizeMB = files[i].size / (1024 * 1024);
        if (fileSizeMB > props.maxSizeMegabyte) {
            errorMessage = `File size exceeds the maximum limit of ${props.maxSizeMegabyte} MB`;
            continue;
        }
        validFiles.push(files[i]);
    }

    if (props.selectedFiles.length >= props.maxFiles && props.maxFiles > 1) {
        errorMessage = `You can only upload up to ${props.maxFiles} files.`;
    }

    if (props.maxFiles == 1) {
        emit('update:selectedFiles', validFiles);
    }

    const remainingSlots = props.maxFiles - props.selectedFiles.length;
    const filesToAdd = validFiles.slice(0, remainingSlots);
    if (filesToAdd.length > 0) {
        if (props.maxFiles > 1) {
            emit('update:selectedFiles', [...props.selectedFiles, ...filesToAdd])
        }
    }

    if (errorMessage) {
        const toast = toastNotification.value;
        if (toast) {
            toast.innerText = errorMessage
            showToast()
        }
    }
};


const handleDragEnter = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = false;
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = false;
    if (e.dataTransfer?.files.length) {
        handleFiles(e.dataTransfer.files);
    }

};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        handleFiles(target.files);
    }
    target.value = '';
};

const handleRemoveFile = (fileToRemove: File, event: MouseEvent) => {
    event.stopPropagation();
    emit('update:selectedFiles', props.selectedFiles.filter((file) => file !== fileToRemove))
};

const formatFileSize = (sizeInBytes: number) => {
    if (sizeInBytes < 1024) {
        return `${sizeInBytes} Bytes`;
    } else if (sizeInBytes < 1024 * 1024) {
        return `${(sizeInBytes / 1024).toFixed(2)} KB`;
    } else if (sizeInBytes < 1024 * 1024 * 1024) {
        return `${(sizeInBytes / (1024 * 1024)).toFixed(2)} MB`;
    } else {
        return `${(sizeInBytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
    }
}

function showToast() {
    toastNotification?.value?.classList.add("show");
    setTimeout(function () {
        toastNotification?.value?.classList.remove("show");
    }, 2000); // Delay 2 detik
}

function displayImage(file: File) {
    return URL.createObjectURL(file)
}
</script>

<style scoped>
.vue-file-upload-energeek .file-upload-list-file {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.vue-file-upload-energeek .file-upload-preview-file {
    background: #fff !important;
    border-radius: 5px;
    width: 140px;
    height: 140px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 5px 5px 15px rgba(128, 128, 128, 0.1);
    justify-content: center;
    position: relative;
    padding: 10px;
}

.vue-file-upload-energeek .remove-file-upload-button {
    position: absolute;
    border-radius: 100px;
    width: 20px;
    display: flex;
    align-items: center;
    box-shadow: 5px 5px 15px rgba(128, 128, 128, 0.3);
    justify-content: center;
    height: 20px;
    background-color: #fff;
    border: 0 !important;
    padding: 0 !important;
    top: -5px;
    right: -5px;
}

.vue-file-upload-energeek .file-upload-preview-name {
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 130px;
}

.vue-file-upload-energeek .file-upload-preview-name:hover {
    overflow: unset !important;
}

.vue-file-upload-energeek .file-upload-preview-size {
    font-size: 16px;
    font-weight: 600;
}


.vue-file-upload-energeek .toast-file-upload {
    visibility: hidden;
    min-width: 250px;
    background-color: red;
    color: #fff;
    text-align: center;
    padding: 16px;
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    opacity: 0;
    z-index: 999999999;
    transition: opacity 0.5s ease-in-out;
}

.vue-file-upload-energeek .toast-file-upload.show {
    visibility: visible;
    opacity: 1;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}
</style>
