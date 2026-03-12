<template>
    <div ref="modalRef" class="modal fade" tabindex="-1">
        <div :class="`modal-dialog modal-dialog-centered ${size}`">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="w-100 text-center">
                        <h1 :class="`modal-title ${textAlignTitle}`">{{ title }}</h1>
                        <div class="text-muted">{{ subtitle }}</div>
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon-2x">
                            <CloseIcon />

                        </span>
                    </button>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { CloseIcon } from '@/components/icons';
import {
    ref,
} from 'vue';

interface Props {
    title: string;
    subtitle: string;
    size?: string;
    textAlignTitle?: string;
}

withDefaults(defineProps<Props>(), {
    size: '',
    textAlignTitle: 'text-center',
});

const modalRef = ref<HTMLDivElement | null>(null);

function show() {
    if (modalRef.value) {
        $(modalRef.value).modal('show');
    }
}

function hide() {
    if (modalRef.value) {
        $(modalRef.value).modal('hide');
    }
}

// Expose methods to parent
defineExpose({
    show,
    hide
});
</script>
