<template>
    <div class="vue-select-multiple-energeek" ref="dropdownRef">
        <div class="select-multiple-box" :style="{ background: disabled ? '#eff2f5' : '#fff' }"
            @click="handleDropdownToggle">
            <div v-if="hasValue(modelValue)" class="select-multiple-selected-value-group">
                <div v-for="(context, index) in modelValue" :key="index"
                    style="font-size: 1.1rem; background: #f3f6f9;border-radius: 5px;padding: 3px 10px;color: black;margin-right: 10px;display: flex;align-items: center;margin-top: 3px;margin-bottom:  3px;">
                    <div style="margin-right: 5px;cursor: pointer;" @click="handleRemoveSelectedValue(index)">
                        <svg width="14px" height="14px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.29 5.29c.39-.39 1.02-.39 1.41 0L12 10.59l5.29-5.3c.39-.39 1.02-.39 1.41 0s.39 1.02 0 1.41L13.41 12l5.3 5.29c.39.39.39 1.02 0 1.41-.39.39-1.02.39-1.41 0L12 13.41l-5.29 5.3c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L10.59 12 5.3 6.71c-.39-.39-.39-1.02 0-1.42z"
                                fill="#0F1729" />
                        </svg>

                    </div>
                    <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" :title="context.text">
                        {{ context.text }}
                    </div>
                </div>
            </div>
            <div v-else
                style="color: #a1a5b7;font-weight: 500;font-size: 1.1rem;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"
                v-html="placeholder || 'Pilih Data'" />

            <div class="d-flex" style="margin-left: 10px">
                <div v-if="hasValue(modelValue) && clearable && !disabled" class="select-multiple-remove-icon"
                    @click="handleRemoveValue">
                    <svg width="14px" height="14px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.29 5.29c.39-.39 1.02-.39 1.41 0L12 10.59l5.29-5.3c.39-.39 1.02-.39 1.41 0s.39 1.02 0 1.41L13.41 12l5.3 5.29c.39.39.39 1.02 0 1.41-.39.39-1.02.39-1.41 0L12 13.41l-5.29 5.3c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L10.59 12 5.3 6.71c-.39-.39-.39-1.02 0-1.42z"
                            fill="#0F1729" />
                    </svg>

                </div>
                <div>
                    <svg height="13px" width="13px" viewBox="0 0 26 15">
                        <polygon fill="#231F20"
                            points="23.303,-0.002 12.467,10.834 1.63,-0.002 -0.454,2.082 12.467,15.002 14.551,12.918 25.387,2.082" />
                    </svg>
                </div>
            </div>
        </div>

        <div v-if="isDropdownOpen" class="select-multiple-option">
            <div v-if="searchable" class="select-multiple-option-search"
                style="border-bottom: 1px solid #e5e5e5;display: flex;align-items: center;">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                            transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                        <path
                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                            fill="currentColor" />
                    </svg>
                </div>
                <input type="text" placeholder="Search...."
                    style="border: 0 !important;outline: none;box-shadow: unset;"
                    @input="handleSearch(); searchParams = ($event.target as HTMLInputElement)?.value">
            </div>
            <div class="select-multiple-option-list">
                <a v-if="loading" class="disabled">Please Waiting...</a>
                <a v-else v-for="(option, idx) in filteredOptions" :key="idx"
                    :class="{ active: checkHasValue(option?.id || ''), 'no-select': option.disabled }"
                    @click="handleSelectOption(option)" v-html="option?.html || option?.text || ''" />
                <a v-if="filteredOptions.length === 0 && !loading" class="disabled">No Data</a>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import _ from 'lodash';

interface ISelectOption {
    id?: string;
    text?: string;
    html?: string;
    disabled?: boolean;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    [key: string]: any;
}

interface IProps {
    options: ISelectOption[];
    loading?: boolean;
    modelValue: ISelectOption[];
    placeholder?: string;
    disabled?: boolean;
    searchable?: boolean;
    serverside?: boolean;
    clearable?: boolean;
}

const props = withDefaults(defineProps<IProps>(), {
    options: () => [],
    loading: false,
    modelValue: () => [],
    placeholder: "Pilih Data",
    disabled: false,
    searchable: true,
    serverside: true,
    clearable: true,
});
const emit = defineEmits(['update:modelValue', 'onSearch', 'onChange', 'onOpen']);

const searchParams = ref('');
const isDropdownOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const filteredOptions = computed(() => {
    if (searchParams.value && !props.serverside) {
        return props.options.filter(option => option.text?.toLowerCase().includes(searchParams.value.toLowerCase()));
    }
    return props.options;
});



const handleDropdownToggle = () => {
    if (props.disabled) return;
    searchParams.value = '';
    isDropdownOpen.value = !isDropdownOpen.value;
    if (isDropdownOpen.value) {
        props.serverside ? emit('onSearch', '', 25) : emit('onOpen');
    }
};

const handleSelectOption = (option: ISelectOption) => {
    if (option.disabled) return;
    const newValue = [...props.modelValue];
    const index = newValue.findIndex(e => e.id === option.id);
    if (index >= 0) {
        newValue.splice(index, 1);
    } else {
        newValue.push(option);
    }
    emit('update:modelValue', newValue);
    emit('onChange', newValue, props.modelValue);
    isDropdownOpen.value = false;
};

const handleRemoveValue = function () {
    emit('onChange', [], props.modelValue);
    emit('update:modelValue', []);

}

const handleRemoveSelectedValue = (index: number) => {
    const newValue = [...props.modelValue];
    newValue.splice(index, 1);
    emit('update:modelValue', newValue);
    emit('onChange', newValue, props.modelValue);
};

const hasValue = (value: ISelectOption[]): boolean => {
    return value.length > 0;
};

const checkHasValue = (id: number | string) => props.modelValue.some(e => e.id === id);


const handleSearch = _.debounce(() => {
    if (props.serverside) {
        emit('onSearch', searchParams.value, 15);
    }
}, 1500);

const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        isDropdownOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    window.removeEventListener("click", handleClickOutside);
});

</script>

<style scoped>
.vue-select-multiple-energeek {
    position: relative;
    width: 100%;
    display: block;
}

.vue-select-multiple-energeek .select-multiple-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem 1rem;
    border: 1px solid #e4e6ef !important;
    background-color: #fff;
    border-radius: '5px';
    padding: .75rem 1rem;
    cursor: pointer;
    font-size: 1.1rem;
}

.vue-select-multiple-energeek .select-multiple-option-search input {
    width: 100%;
    padding: 10px 0;
    margin-left: 10px;
    color: black;
    font-size: 1.1rem;
}

.vue-select-multiple-energeek .select-multiple-option {
    position: absolute;
    width: 100%;
    z-index: 99999;
    background-color: #fff;
    border: 1px solid rgb(229, 229, 229);
    border-top: 0 !important;

}

.vue-select-multiple-energeek .select-multiple-option-list {
    max-height: 200px;
    overflow: auto;
}

.vue-select-multiple-energeek .select-multiple-option-list a {
    color: black;
    padding: 10px;
    width: 100%;
    font-size: 1.1rem;
    width: 100% !important;
    display: block;
    cursor: pointer;
}

.vue-select-multiple-energeek .select-multiple-option-list a:hover,
.vue-select-multiple-energeek .select-multiple-option-list a.active {
    background-color: #f2f2f2 !important;
}

.vue-select-multiple-energeek .select-multiple-option-list a.disabled {
    cursor: no-drop;
    background-color: #fff !important;
}

.vue-select-multiple-energeek .select-multiple-option-list a.no-select {
    cursor: no-drop;
    background-color: #f2f2f2 !important;
}

.vue-select-multiple-energeek .select-multiple-remove-icon {
    margin-right: 5px;
}

.vue-select-multiple-energeek .select-multiple-remove-icon svg path {
    fill: black;
}

.vue-select-multiple-energeek .select-multiple-remove-icon:hover svg path {
    fill: red;
}

.vue-select-multiple-energeek .select-multiple-selected-value-group {
    display: flex;
    flex-wrap: wrap;
}


.vue-select-multiple-energeek .select-multiple-selected-value-group svg path {
    fill: black;
}

.vue-select-multiple-energeek .select-multiple-selected-value-group svg:hover path {
    fill: red;
}

.d-flex {
    display: flex;
}
</style>
