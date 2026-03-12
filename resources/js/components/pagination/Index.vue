<template>
    <ul class="vue-pagination-energeek">
    <li>
      <button :disabled="isInFirstPage || totalData === 0" @click="onClickFirstPage">
        First
      </button>
    </li>
    <li>
      <button :disabled="isInFirstPage" @click="onClickPreviousPage">
        &#8592;
      </button>
    </li>
    <li v-for="(item, index) in pages" :key="index">
      <button 
        :class="{ active: currentPage === item.page }" 
        :disabled="item.isDisabled || totalData === 0" 
        @click="onClickPage(item.page)">
        {{ item.page }}
      </button>
    </li>
    <li>
      <button :disabled="isInLastPage || totalData === 0" @click="onClickNextPage">
        &#8594;
      </button>
    </li>
    <li>
      <button :disabled="isInLastPage || totalData === 0" @click="onClickLastPage">
        Last
      </button>
    </li>
  </ul>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue';
    type Page = {
        page: number;
        isDisabled: boolean;
    };
    interface PaginationProps {
        totalData: number,
        currentPage: number,    
        limit: number,       
        isFromStore?: boolean 
    }

    const props = withDefaults(defineProps <PaginationProps> (), {
        isFromStore: false
    });
    const emit = defineEmits(['update:currentPage','changePage'])

    const maxVisibleButtons = ref(5);    

    const totalPages = computed<number>(() => {
        return Math.ceil(props.totalData / props.limit);
    })

    const startPage = computed<number>(() => {
        if (props.currentPage == 1) {
            return 1;
        }

        if (props.currentPage == totalPages.value) {
            return totalPages.value - (maxVisibleButtons.value - 1);
        }

        return props.currentPage - 1;
    })

    const pages = computed<Page[]>(() => {
        const range : Page[] = [];

        for (let x = startPage.value; x <= Math.min(startPage.value + maxVisibleButtons.value - 1, totalPages.value); x++) {
            if (x > 0) {
                range.push({
                    page: x,
                    isDisabled: x == props.currentPage
                });
            }

        }

        if (range.length == 3 && (props.currentPage + 1) == totalPages.value) {
            if (range[0].page - 1 > 0) {
                range.unshift({
                    page: range[0].page - 1,
                    isDisabled: false,
                })
            }
        }

        if (range.length == 4 && (props.currentPage + 1) == totalPages.value || (props.currentPage + 2) == totalPages.value) {
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
        return props.currentPage == 1;
    })
    
    const isInLastPage = computed<boolean>(() => {
        return props.currentPage == totalPages.value;
    })

    function changePage(page: number) {   

        if(props.isFromStore === false){
            emit('update:currentPage', page)
        }
        
        emit('changePage', page)
    }

    function onClickFirstPage() {        
        changePage(1);
    }

    function onClickPreviousPage() {
        changePage(props.currentPage - 1);
    }

    function onClickPage(page: number) {
        changePage(page);
    }

    function onClickNextPage() {
        changePage(props.currentPage + 1);
    }

    function onClickLastPage() {
        changePage(totalPages.value);
    }
    
  </script>
  
  <style scoped>
    .vue-pagination-energeek{
        display: flex;
        flex-wrap: wrap;
        list-style-type: none;
        margin-bottom: 15px;
    }

    .vue-pagination-energeek  button {
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
    }

    .vue-pagination-energeek  button.btn-prev-next-pagination {
        background: #F7F9FB !important;
        border-radius: 8px !important;
    }

    .vue-pagination-energeek button.active {
        background: #5089C6 !important;
        border-radius: 8px;
        color: #fff !important;
    }

  </style>