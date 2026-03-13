import api from '@services/api';
import { defineStore } from 'pinia';
import { reactive } from 'vue';
import { ICategoryPayload, ICategory } from '@src/types/category';
import { axiosHandleError } from '@/plugins/global';

export const useCategoryStore = defineStore('category', () => {

    // State
    const table = reactive({
        column: [
            { text: 'NO', sortBy: 'id', sortColumn: true, class: 'w-10px pe-2 text-center' },
            { text: 'Nama', sortBy: 'name', sortColumn: true, class: 'min-w-125px' },
            { text: 'Tipe', sortBy: 'type', sortColumn: true, class: 'min-w-125px' },
            { text: 'Warna / Ikon', sortBy: '', sortColumn: false, class: 'min-w-125px' },
            { text: 'Opsi', sortBy: '', sortColumn: false, class: 'text-center min-w-100px' }
        ],
        data: [] as ICategory[],
        showPerPage: 10,
        search: '',
        order: 'desc',
        sortBy: 'created_at',
        totalData: 0,
        currentPage: 1,
        loading: false,
        showSearch: true,
    })

    // Actions
    function setShowPerPage(perPage: number) { table.showPerPage = perPage }
    function setCurrentPage(page: number) { table.currentPage = page }
    function setLoading(loading: boolean) { table.loading = loading }
    function setSearch(value: string) { table.search = value }
    function setData(data: ICategory[]) { table.data = data }
    function setOrder(order: string) { table.order = order }
    function setSortBy(sort: string) { table.sortBy = sort }
    function setTotalData(total: number) { table.totalData = total }
    function resetTable() {
        setCurrentPage(1)
        setShowPerPage(10)
        setSearch('')
        setTotalData(0)
        setOrder('desc')
        setSortBy('created_at')
    }

    async function getData() {
        setLoading(true)
        try {
            const params = {
                page: table.currentPage,
                limit: table.showPerPage,
                order_by: table.order,
                sort_by: table.sortBy,
                search: table.search
            }
            const res = await api().get('api/categories', { params })
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

    async function getAll(page: number = 1, perPage: number = 15) {
        const response = await api().get('api/categories', {
            params: { page, limit: perPage }
        });
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/categories/${id}`);
        return response;
    }

    async function create(payload: ICategoryPayload) {
        const response = await api().post('api/categories', payload);
        return response;
    }

    async function update(id: string, payload: ICategoryPayload) {
        const response = await api().put(`api/categories/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/categories/${id}`);
        return response;
    }

    return { 
        table, setShowPerPage, setCurrentPage, setLoading, setSearch, 
        setData, setOrder, setSortBy, setTotalData, resetTable, getData,
        getAll, getById, create, update, destroy 
    };
});
