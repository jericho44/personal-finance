import { defineStore } from 'pinia'
import { reactive } from 'vue'
import api from '@services/api'
import type { IDataTableNewsCategory, ICreateNewsCategoryPayload, IEditNewsCategoryPayload, INewsCategory } from '@/types/news-category'
import { axiosHandleError } from '@/plugins/global'

const prefix = 'api-web'
const resource = '/master/news-category'

export const useNewsCategory = defineStore('newsCategory', () => {
    // State
    const table = reactive<IDataTableNewsCategory>({
        column: [
            { text: 'NO', sortBy: 'id', sortColumn: true },
            { text: 'Nama', sortBy: 'name', sortColumn: true, class: 'w-300px' },
            { text: 'Status', sortBy: 'id', sortColumn: false, class: 'text-center' },
            { text: 'Aksi', sortBy: '', sortColumn: false, class: 'text-center' }
        ],
        data: [],
        showPerPage: 10,
        search: '',
        order: 'asc',
        sortBy: 'id',
        totalData: 0,
        currentPage: 1,
        loading: false,
        showSearch: true,
    })

    // Actions
    function setShowPerPage(perPage: number) {
        table.showPerPage = perPage
    }

    function setCurrentPage(page: number) {
        table.currentPage = page
    }

    function setLoading(loading: boolean) {
        table.loading = loading
    }

    function setSearch(value: string) {
        table.search = value
    }

    function setData(data: INewsCategory[]) {
        table.data = data
    }

    function setOrder(order: string) {
        table.order = order
    }

    function setSortBy(sort: string) {
        table.sortBy = sort
    }

    function setTotalData(total: number) {
        table.totalData = total
    }

    function resetTable() {
        setCurrentPage(1)
        setShowPerPage(10)
        setSearch('')
        setTotalData(0)
        setOrder('asc')
        setSortBy('id')
    }

    async function getData() {
        setLoading(true)
        try {
            const params = {
                page: table.currentPage,
                limit: table.showPerPage,
                orderBy: table.order,
                sortBy: table.sortBy,
                search: table.search
            }
            const res = await api().get(`${prefix}${resource}`, { params })
            setData(res.data.data.data)
            setTotalData(res.data.data.total)
            return res
        } catch (err) {
            setData([])
            setTotalData(0)
            axiosHandleError(err)
            return err
        } finally {
            setLoading(false)
        }
    }

    async function create(payload: ICreateNewsCategoryPayload) {
        return api().post(`${prefix}${resource}`, payload)
    }

    async function show(id: string | number) {
        return api().get(`${prefix}${resource}/${id}`)
    }

    async function update(id: string | number, payload: IEditNewsCategoryPayload) {
        return api().put(`${prefix}${resource}/${id}`, payload)
    }

    async function changeStatus(id: string | number) {
        return api().put(`${prefix}${resource}/${id}/change-status`)
    }

    return {
        table,
        setShowPerPage,
        setCurrentPage,
        setLoading,
        setSearch,
        setData,
        setOrder,
        setSortBy,
        setTotalData,
        resetTable,
        getData,
        create,
        show,
        changeStatus,
        update,
    }
})
