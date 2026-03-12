<template>
    <div class="vue-datatable-energeek">
        <div class="datatable-header">
            <div class="datatable-left-header">
                <select :value="props.config.showPerPage" class="select-show-per-page" @change="changeShowPerPage"
                    v-if="mode == 'v1'">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <div class="d-flex" style="border: 1px #ddd solid; border-radius: 3px" v-if="mode == 'v2'">
                    <div style="background: #fff; padding: 5px 15px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                            <path
                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                fill="currentColor" />
                        </svg>
                    </div>
                    <input type="text" class="search-data" :value="props.config.search"
                        @input="(evt: any) => { changeSearch(evt); getDataAfterSearch(); }" placeholder="Cari">
                </div>
            </div>
            <div class="datatable-right-header">
                <div class="d-flex me-3" style="border: 1px #ddd solid; border-radius: 3px" v-if="mode == 'v1'">
                    <div style="background: #fff; padding: 5px 15px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                            <path
                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                fill="currentColor" />
                        </svg>
                    </div>
                    <input type="text" class="search-data" :value="props.config.search"
                        @input="(evt: any) => { changeSearch(evt); getDataAfterSearch(); }" placeholder="Cari">
                </div>
                <slot name="additionalButton" />
            </div>
        </div>

        <div class="datatable-body">
            <table class="custom-vue-datatable">
                <thead>
                    <tr>
                        <th v-for="(item, index) in props.config.column" :key="index" :class="item.class"
                            @click="changeSortBy(item.sortBy)" :style="item.sortColumn ? { cursor: 'pointer' } : {}">
                            {{ item.text }}
                            <span v-if="item.sortColumn !== false">
                                <template v-if="props.config.sortBy === item.sortBy">
                                    <svg v-if="props.config.order === 'asc'" enable-background="new 0 0 26 15"
                                        height="13px" version="1.1" viewBox="0 0 26 15" width="13px"
                                        xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <polygon fill="#231F20"
                                            points="23.303,15.002 12.467,4.166 1.63,15.002 -0.454,12.918 12.467,-0.002 14.551,2.082 25.387,12.918" />
                                    </svg>
                                    <svg v-else enable-background="new 0 0 26 15" height="13px" id="Layer_1"
                                        version="1.1" viewBox="0 0 26 15" width="13px" xml:space="preserve"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <polygon fill="#231F20"
                                            points="23.303,-0.002 12.467,10.834 1.63,-0.002 -0.454,2.082 12.467,15.002 14.551,12.918 25.387,2.082" />
                                    </svg>
                                </template>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="props.config.loading">
                        <td class="text-center" :colspan="props.config.column.length">
                            <div class="loader" />
                        </td>
                    </tr>
                    <template v-else>
                        <slot name="body" />
                    </template>
                    <tr v-if="props.config.data.length === 0 && props.config.loading === false">
                        <td class="text-center" :colspan="props.config.column.length">Data Not Found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="datatable-footer">
            <div className="datatable-footer-left">
                <template v-if="mode == 'v1'">
                    Showing {{ Math.min((props.config.currentPage - 1) * props.config.showPerPage + 1,
                        props.config.totalData) }} to {{ Math.min(props.config.currentPage * props.config.showPerPage,
                        props.config.totalData) }} of {{ props.config.totalData }} records
                </template>
                <template v-if="mode == 'v2'">
                    <select :value="props.config.showPerPage" class="select-show-per-page" @change="changeShowPerPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </template>
            </div>
            <div class="datatable-footer-right">
                <ul class="custom-vue-datatable-pagination">
                    <li><button :disabled="isInFirstPage" @click="onClickFirstPage">First</button></li>
                    <li><button :disabled="isInFirstPage" @click="onClickPreviousPage">&#8592;</button></li>
                    <li v-for="(item, index) in pages" :key="index">
                        <button :class="{ active: props.config.currentPage === item.page }"
                            @click="onClickPage(item.page)" :disabled="item.isDisabled">
                            {{ item.page }}
                        </button>
                    </li>
                    <li><button :disabled="isInLastPage" @click="onClickNextPage">&#8594;</button></li>
                    <li><button :disabled="isInLastPage" @click="onClickLastPage">Last</button></li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import _ from "lodash";
import {
    ref, computed
} from 'vue';

type Page = {
    page: number;
    isDisabled: boolean;
}
interface IDataTableColumn {
    text: string,
    sortColumn: boolean,
    sortBy?: string,
    class?: string
}


interface IDataTable {
    column: IDataTableColumn[],
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    data: any[],
    showPerPage: number,
    search: string,
    order: string,
    sortBy: string,
    totalData: number,
    currentPage: number,
    loading: boolean,
    showSearch: boolean,
}

interface IProps {
    config: IDataTable,
    search?: IDataTable["search"],
    showPerPage?: IDataTable["showPerPage"],
    order?: IDataTable["order"],
    sortBy?: IDataTable["sortBy"],
    currentPage?: IDataTable["currentPage"],
    isFromStore?: boolean,
    mode?: string;
}

const props = withDefaults(defineProps<IProps>(), {
    search: '',
    showPerPage: 10,
    order: 'asc',
    sortBy: '',
    currentPage: 1,
    isFromStore: false,
    mode: 'v2',
});
const emit = defineEmits(['update:search', 'update:showPerPage', 'update:order', 'update:sortBy', 'update:currentPage', 'setSearch', 'setOrder', 'setSortBy', 'setShowPerPage', 'setPage', 'getData'])


const maxVisibleButtons = ref(5);

const totalPages = computed<number>(() => {
    return Math.ceil(props.config.totalData / props.config.showPerPage);
})

const startPage = computed<number>(() => {
    if (props.config.currentPage == 1) {
        return 1;
    }

    if (props.config.currentPage == totalPages.value) {
        return totalPages.value - (maxVisibleButtons.value - 1);
    }

    return props.config.currentPage - 1;
})

const pages = computed<Page[]>(() => {
    const range: Page[] = [];

    for (let x = startPage.value; x <= Math.min(startPage.value + maxVisibleButtons.value - 1, totalPages.value); x++) {
        if (x > 0) {
            range.push({
                page: x,
                isDisabled: x == props.config.currentPage
            });
        }

    }

    if (range.length == 3 && (props.config.currentPage + 1) == totalPages.value) {
        if (range[0].page - 1 > 0) {
            range.unshift({
                page: range[0].page - 1,
                isDisabled: false,
            })
        }
    }

    if (range.length == 4 && (props.config.currentPage + 1) == totalPages.value || (props.config.currentPage + 2) == totalPages.value) {
        if (range[0].page - 1 > 0) {
            range.unshift({
                page: range[0].page - 1,
                isDisabled: false,
            })
        }
    }

    return range;
});

const isInFirstPage = computed<boolean>(() => {
    return props.config.currentPage == 1;
})

const isInLastPage = computed<boolean>(() => {
    return props.config.currentPage == totalPages.value;
})

const changeShowPerPage = function (event: Event) {
    const target = event.target as HTMLSelectElement;

    if (props.isFromStore === false) {
        emit('update:showPerPage', Number(target.value) || 1);
        emit('update:currentPage', 1)
    }
    emit('setShowPerPage', Number(target.value) || 10);
    emit('setPage', 1);
    emit('getData');
}

const changeSearch = function (event: Event) {
    const target = event.target as HTMLInputElement;
    if (props.isFromStore === false) {
        emit('update:search', target.value);
    }

    emit('setSearch', target.value)
}

const getDataAfterSearch = _.debounce(() => {
    if (props.isFromStore === false) {
        emit('update:currentPage', 1);
    }

    emit('setPage', 1)
    emit('getData');
}, 1000);

const changeSortBy = function (sortBy?: string) {
    if (props.config.loading === true) {
        return false;
    }

    if (sortBy) {
        let order = '';
        if (sortBy == props.config.sortBy) {
            if (props.config.order == 'asc')
                order = 'desc';
            else if (props.config.order == 'desc')
                order = 'asc';
            else
                order = 'asc';
        } else {
            order = 'asc';
        }

        if (props.isFromStore === false) {
            emit('update:currentPage', 1);
            emit('update:sortBy', sortBy);
            emit('update:order', order);
        }

        emit('setSortBy', sortBy);
        emit('setOrder', order);
        emit('setPage', 1);
        emit('getData');
    }
}

const changePage = function (page: number) {
    if (props.isFromStore === false) {
        emit('update:currentPage', page);
    }
    emit('setPage', page);
    emit('getData')
}
const onClickFirstPage = function () {
    changePage(1)
}
const onClickPreviousPage = function () {
    changePage(props.config.currentPage - 1)
}
const onClickPage = function (page: number) {
    changePage(page)
}
const onClickNextPage = function () {
    changePage(props.config.currentPage + 1);
}
const onClickLastPage = function () {
    changePage(totalPages.value)
}


</script>

<style>
.vue-datatable-energeek {
    width: 100%;
}

.d-flex {
    display: flex;
}

.vue-datatable-energeek .datatable-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.datatable-right-header {
    display: flex;
    position: relative;
    gap: 5px;
}

@media(max-width:991px) {
    .vue-datatable-energeek .datatable-header {
        display: block;
    }
}

.vue-datatable-energeek .datatable-body {
    width: 100%;
    overflow-x: auto;
}

.vue-datatable-energeek .datatable-header .datatable-left-header,
.vue-datatable-energeek .datatable-header .datatable-right-header {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.custom-search {

    border: 1px solid #E4E6EF;
    border-radius: 5px;
    background: #fff;
    padding: 5px 10px;
    width: 100%;
    max-width: 427px;

}

.vue-datatable-energeek .datatable-header .datatable-right-header {
    justify-content: flex-end;
}



.vue-datatable-energeek .select-show-per-page {
    display: block;
    width: 100%;
    padding: 7px 10px;
    font-size: 1rem;
    outline: none !important;
    background-color: #f2f2f2;
    font-weight: 400;
    line-height: 1.5;
    border: 1px #ddd solid;
    border-radius: 3px;
}

.vue-datatable-energeek .search-data {
    display: block;
    padding: 5px 0;
    width: 100%;
    min-width: 180px;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    outline: 0 !important;
    border: 0 !important;
}


.vue-datatable-energeek .loader {
    border: 5px solid #f3f3f3;
    /* Light grey */
    border-top: 5px solid #0069B5;
    /* Blue */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin: 0 auto;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.vue-datatable-energeek .datatable-footer {
    display: flex;
    margin-top: 15px;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.vue-datatable-energeek .custom-vue-datatable-pagination {
    display: flex;
    flex-wrap: wrap;
    list-style-type: none;
    margin-bottom: 15px;
}

.vue-datatable-energeek .custom-vue-datatable-pagination button {
    padding: 10px;
    background-color: #fff !important;
    border: 0 !important;
    width: 35px;
    display: flex;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    margin: 5px;
    height: 35px;
    font-size: 11px !important;
}

.vue-datatable-energeek .custom-vue-datatable-pagination button.btn-prev-next-datatable {
    background: #F7F9FB !important;
    border-radius: 8px !important;
}

.vue-datatable-energeek .custom-vue-datatable-pagination button.active {
    background: #0069B5 !important;
    border-radius: 8px;
    color: #fff !important;
}

.vue-datatable-energeek .custom-vue-datatable {
    width: 100%;
}

.vue-datatable-energeek .custom-vue-datatable thead tr th {
    border-bottom: 1px #eaebed solid !important;
    padding: 15px 5px;
    color: #3F4254;
    font-size: 14px;
    background-color: white;
    font-weight: 600 !important;
    cursor: pointer;
}

.vue-datatable-energeek .custom-vue-datatable tbody tr td {
    border-bottom: 1px #eaebed solid !important;
    padding: 15px 5px;
}

.custom-vue-datatable {
    min-width: 600px;
}
</style>
