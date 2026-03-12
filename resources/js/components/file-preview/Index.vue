<template>
    <div class="vue-file-preview-energeek">
        <div class="vue-file-preview-left">
            <div>
                <div style="margin-top: 5px;" v-if="getIcon()">
                    <component v-if="getIcon()" :is="getIcon()" />
                    
                </div>
            </div>
            <div class="file-info-group">
                <div v-if="isImage" class="file-name" @click="isPreviewOpen = true" style="cursor: pointer;">
                    {{ name }}
                </div>
                <a v-else :href="url" target="_blank" class="file-name" rel="noopener noreferrer">
                    {{ name }}
                </a>
                <div class="file-size">{{ formatFileSize(props.sizeBytes) }}</div>
            </div>
        </div>
        <div class="vue-file-preview-right">
            <button v-if="removable" class="remove-button" type="button" @click.prevent="emit('on-remove')"
                style="cursor: pointer;">
                <svg width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.31658 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                        fill="#0F1729" />
                </svg>
            </button>
        </div>
        <div v-if="isPreviewOpen" class="modal-overlay-file-preview" @click="isPreviewOpen = false">
            <button class="close-button-file-preview" @click="isPreviewOpen = false">×</button>
            <div class="modal-content-file-preview" @click.stop>
                <img ref="imagePreview" :src="url" :alt="name" style="max-width: 100%; max-height: 100%;"
                    @click="imagePreviewOnClick($event)">
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import iconDoc from "./icon-doc.vue";
import iconExcel from "./icon-excel.vue";
import iconImage from "./icon-image.vue";
import iconPdf from "./icon-pdf.vue";

import {
    ref,
} from 'vue'

interface Props {
    ext: string;
    url: string;
    removable?: boolean;
    name?: string;
    sizeBytes?: number;
}

const props = withDefaults(defineProps<Props>(), {
    removable: false, // Default value for removable
    name: "", // Default empty string for name
    sizeBytes: 0, // Default size of 0 bytes
});

const emit = defineEmits(['on-remove'])

const isPreviewOpen = ref<boolean>(false);
const imagePreview = ref<HTMLImageElement | null>(null);

const formatFileSize = (sizeInBytes: number) => {
    if (sizeInBytes < 1024) return `${sizeInBytes} Bytes`;
    if (sizeInBytes < 1024 * 1024) return `${(sizeInBytes / 1024).toFixed(2)} KB`;
    if (sizeInBytes < 1024 * 1024 * 1024) return `${(sizeInBytes / (1024 * 1024)).toFixed(2)} MB`;
    return `${(sizeInBytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
};

const getIcon = () => {
    if (["pdf", "application/pdf"].includes(props.ext)) return iconPdf;
    if (["doc", "docx", "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    ].includes(props.ext)) return iconDoc;
    if (["xls", "xlsx", "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    ].includes(props.ext)) return iconExcel;
    if (["jpg", "jpeg", "png", "gif", "image/jpeg", "image/png", "image/gif"].includes(props.ext)) return iconImage;
    return '';
};

function imagePreviewOnClick(e: MouseEvent) {
    e.stopPropagation()
}

const isImage = ["jpg", "jpeg", "png", "gif", "image/jpeg", "image/png", "image/gif"].includes(props.ext);
</script>

<style scoped>
.vue-file-preview-energeek {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.vue-file-preview-energeek .vue-file-preview-left {
    display: flex;
    align-items: flex-start;
}

.vue-file-preview-energeek .vue-file-preview-left img {
    margin-top: 5px;
}

.vue-file-preview-energeek .vue-file-preview-left .file-info-group {
    padding-left: 10px;
}

.vue-file-preview-energeek .vue-file-preview-left .file-info-group .file-name {
    font-size: 15px;
    color: black !important;
    text-decoration: none;
    font-weight: 500;
}

.vue-file-preview-energeek .vue-file-preview-left .file-info-group .file-size {
    color: #a1a5b7 !important;
    font-size: 14px !important;
}

.vue-file-preview-energeek .vue-file-preview-right svg path {
    fill: red;
}

.vue-file-preview-energeek .vue-file-preview-right .remove-button {
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    outline: none !important;
}

.vue-file-preview-energeek .modal-overlay-file-preview {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.9);
    /* Black background with transparency */
    display: flex;
    justify-content: center;
    animation: fadeIn 1s forwards;
    /* Fade-in animation */
    z-index: 9999999999;
    /* Ensure it's on top of other content */
}

.vue-file-preview-energeek .modal-content-file-preview {
    background-color: transparent;
    /* Modal content background */
    padding: 15px;
    border-radius: 8px;
    max-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 95%;
    position: relative;
}

/* Close button style */
.vue-file-preview-energeek .close-button-file-preview {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(24, 24, 27, .65);
    padding: 10px !important;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 50px;
    color: #fff;
    cursor: pointer;

    z-index: 2;
}

@media(max-width:991px) {
    .vue-file-preview-energeek .close-button-file-preview {
        font-size: 40px;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>
