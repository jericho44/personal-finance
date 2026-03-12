<script setup>
import _ from "lodash";

import {
    ref,
    computed,
    onMounted,
} from 'vue'

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        required: false,
        default: false,
    },
    modelValue: {
        type: Object,
        required: false,
        default: null,
    },
    placeholder: {
        type: String,
        required: false,
        default: '',
    },

    name: {
        type: String,
        required: false,
        default: '',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false
    },

    show_search: {
        type: Boolean,
        required: false,
        default: true
    },
    serverside: {
        type: Boolean,
        required: false,
        default: false
    },
})

const emit = defineEmits(['change-search', 'option-change', 'option-open', 'update:modelValue'])

const search_params = ref('')
const data = computed(() => {
    const response = [];

    props.options.forEach((z) => {
        if (!props.serverside) {
            if (search_params.value === '') {
                response.push(z)
            } else if (z.text.toLowerCase().includes(search_params.value.toLowerCase())) {
                response.push(z)
            }
        } else {
            response.push(z)
        }

    })

    return response;
});


const selected_data = computed(() => props.modelValue)

onMounted(() => {
    window.onclick = function (event) {
        if (!event.target.classList.contains('dissalow-vue-select-option')) {
            const dropdowns = document.getElementsByClassName("dropdown-vue-content");
            let i;

            for (i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
});

const changeSearch = _.debounce(($event) => {
    emit('change-search', $event.target.value, 15)
}, 800);

function openDropdown(evt) {
    evt.preventDefault();
    evt.stopImmediatePropagation();

    if (props.disabled)
        return;

    search_params.value = '';
    const target = (evt.target) ? evt.target : evt.srcElement;
    const conteks = findAncestor(target, 'dropdown-vue-wrapper');

    if (conteks) {
        for (let i = 0; i < conteks.childNodes.length; i++) {
            if (conteks.childNodes[i].classList && conteks.childNodes[i].classList.contains(
                "dropdown-vue-content")) {
                if (conteks.childNodes[i].classList.contains('show')) {
                    conteks.childNodes[i].classList.remove('show');
                } else {
                    search_params.value = '';
                    toggleOff()
                    conteks.childNodes[i].classList.toggle("show");
                    emit('option-open', target)

                    if (props.serverside) {
                        emit('change-search', search_params.value, 15);
                    }


                }

                break;
            }
        }
    }

}

function findAncestor(el, cls) {
    while ((el = el.parentElement) && !el.classList.contains(cls));
    return el;
}

function toggleOff() {
    const dropdowns = document.getElementsByClassName("dropdown-vue-content");
    let i;
    for (i = 0; i < dropdowns.length; i++) {
        const openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
        }
    }
}

function select_item(evt, option) {
    evt.preventDefault();
    evt.stopImmediatePropagation();

    const lastVal = props.modelValue;

    const conteks = findAncestor(evt.target, 'dropdown-vue-content');
    conteks.classList.remove('show');

    const index = lastVal.findIndex((e) => e.id == option.id);

    let newValue;

    if (index >= 0) {

        newValue = lastVal;
        newValue.splice(index, 1);

    } else {
        newValue = lastVal;
        newValue.push({
            id: option.id,
            text: option.text
        });
    }

    emit('update:modelValue', newValue);
    emit("option-change", {
        value: newValue,
        lastValue: lastVal
    })

}

function checkHasValue(id) {
    if (props.modelValue.filter((e) => e.id == id).length > 0) {
        return true;
    } else {
        return false;
    }
}

function removeValue(index) {
    const lastValue = props.modelValue;
    const newValue = lastValue;
    newValue.splice(index, 1);

    emit('update:modelValue', newValue);
    emit("option-change", {
        value: newValue,
        lastValue: lastValue
    })
}

function removeAllValue() {
    const lastValue = props.modelValue;
    const newValue = [];

    emit('update:modelValue', newValue);
    emit("option-change", {
        value: newValue,
        lastValue: lastValue
    })
}

</script>

<template>
    <div>
        <div id="select-vue-component">
            <div class="dropdown-vue-wrapper">
                <div @click="openDropdown"
                    style="border:1px solid #e4e6ef;border-radius:5px;padding:.75rem 1rem;cursor:pointer"
                    :style="props.disabled ? 'background:#eff2f5 !important;' : 'background:#fff;'"
                    class="d-flex justify-content-between align-items-center">
                    <div style="margin-right:5px;">
                        <div style="display:flex;flex-wrap:wrap;">
                            <div v-for="(context, index) in selected_data"
                                style="font-size:14px;background: #F3F6F9;border-radius: 5px;padding:3px 10px;color:black;margin-right:10px;display:flex;align-items:center;margin-top:3px;margin-bottom:3px;">
                                <div class="icon-remove-option-multiselect" @click="removeValue(index)"
                                    style="margin-right:5px;"><i class="fa fa-times" /></div>
                                <div>{{ context.text }}</div>
                            </div>
                        </div>
                        <div style="color:#a1a5b7;font-weight:500;font-size:1.1rem" v-if="selected_data.length == 0">
                            {{ props.placeholder ? props.placeholder : "Pilih Data" }}</div>
                    </div>
                    <div v-if="selected_data.length > 0" @click="removeAllValue()"
                        class="icon-remove-all-option-multiselect"><i class="fa fa-times" /></div>
                </div>
                <div :class="'dropdown-vue-content dissalow-vue-select-option'">
                    <div v-if="show_search" class="dropdown-vue-search-wrap dissalow-vue-select-option"
                        style="border-bottom:1px solid #e5e5e5;display:flex;align-items: center;">
                        <span class="svg-icon svg-icon-2x svg-icon-gray-500" style="margin-left:10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                    transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <input v-model="search_params" class="form-control dissalow-vue-select-option"
                            @input="changeSearch"
                            style="border:0 !important;outline:none !important;box-shadow:unset !important"
                            placeholder="Search">
                    </div>

                    <div>
                        <div class="dropdown-vue-content-item">
                            <template v-if="loading">
                                <a class="disabled">
                                    Harap Tunggu...
                                </a>
                            </template>

                            <template v-else>
                                <a v-for="(option, idx) in data" :key="idx"
                                    :class="checkHasValue(option.id) ? 'active' : ''"
                                    @click="select_item($event, option)">{{ option.text }}</a>
                                <a v-if="data.length == 0" class="disabled">Tidak Ada Data</a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.dropdown-vue-conteks {
    background: #fff;
    background-position: right 10px top;
    /* Change the px value here to change the distance */
    background-color: #fff !important;
    color: #6c757d;
    padding: 10px 8px;
    border: 1px solid #e5e5e5;
    font-size: 15px !important;
    cursor: pointer;
}

.dropdown-vue-conteks.disabled {
    cursor: no-drop;
    background: #e9ecef !important;
}

.dropdown-vue-wrapper {
    position: relative;
    display: block;
    width: 100%;
}

.dropdown-vue-content {
    display: none;
    position: absolute;
    background-color: white;
    width: 100%;
    overflow: auto;
    border: 1px solid #e5e5e5;
    border-bottom: 2px solid #ddd;
    z-index: 999;
}


.dropdown-vue-content a {
    padding: 10px 15px;
    text-decoration: none;
    display: block;
    color: black !important;
    font-size: 13px;
    font-weight: normal !important;
}

.dropdown-vue-content a.disabled {
    cursor: context-menu;
}

.dropdown-vue-content a.active {
    background: #f2f2f2;
    cursor: context-menu;
}

.dropdown-vue-content-item {
    min-height: 10px;
    max-height: 190px;
    overflow-y: auto;
}

.dropdown-vue-wrapper a:not(.disabled):hover {
    background-color: #f2f2f2;
    color: inherit !important;
}

.dropdown-vue-search-wrap {
    padding: 5px;
}

.show {
    display: block;
}

.select-vue-button-tambah {
    border-top: 1px solid #eee;
    text-align: center;
    margin-top: 5px;
    cursor: pointer;
    background: #fafafa;
}

.icon-remove-option-multiselect i {
    transition: 0.5s;
}

.icon-remove-option-multiselect:hover i {
    color: red;
    transition: 0.5s;
}

.icon-remove-all-option-multiselect i {
    transition: 0.5s;
}

.icon-remove-all-option-multiselect:hover i {
    color: red;
    transition: 0.5s;
}
</style>
