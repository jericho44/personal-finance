import { defineStore } from 'pinia'
import api from '@/services/api'
import { axiosHandleError } from '@/plugins/global'
import type { ISelectListDay, ISelectListDefault, ISelectListRole } from '@/types/selectList'

// ✅ Buat interface khusus untuk state
interface SelectListState {
    selectListLoading: boolean
    selectListRole: ISelectListRole[]
    selectListDay: ISelectListDay[]
    selectListNewsCategory: ISelectListDefault[]
    selectListEventCategory: ISelectListDefault[]
    selectListPlatform: ISelectListDefault[]
    selectListEvent: ISelectListDefault[]
    selectListNews: ISelectListDefault[]
    selectListClient: ISelectListDefault[]
    selectListProjectType: ISelectListDefault[]
}

export const useSelectListStore = defineStore('selectList', {
    state: (): SelectListState => ({
        selectListLoading: false,
        selectListRole: [],
        selectListDay: [],
        selectListNewsCategory: [],
        selectListEventCategory: [],
        selectListPlatform: [],
        selectListEvent: [],
        selectListNews: [],
        selectListClient: [],
        selectListProjectType: [],
    }),

    actions: {
        setLoading(loading: boolean) {
            this.selectListLoading = loading
        },

        async fetchSelectList<T>(
            endpoint: string,
            key: keyof SelectListState, // ✅ tipe key benar sekarang
            search = '',
            limit = 10,
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            filter: Record<string, any> = {}
        ) {
            const params = { search, limit, ...filter }
            this.setLoading(true);
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            (this[key] as any) = []

            try {
                const response = await api().get(endpoint, { params })
                const result = response.data.data.map(({ id, name }: { id: string; name: string }) => ({
                    id,
                    text: name,
                })) as T[];
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                (this[key] as any) = result
                return response
            } catch (error) {
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                (this[key] as any) = []
                axiosHandleError(error)
                throw error
            } finally {
                this.setLoading(false)
            }
        },

        getSelectListRole(search: string, limit: number) {
            return this.fetchSelectList<ISelectListRole>('/api/select-list/role', 'selectListRole', search, limit
            )
        },
        getSelectListDay(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDay>('/api/select-list/day', 'selectListDay', search, limit
            )
        },
        getSelectListNewsCategory(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/news-category', 'selectListNewsCategory', search, limit
            )
        },
        getSelectListEventCategory(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/event-category', 'selectListEventCategory', search, limit
            )
        },
        getSelectListPlatform(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/platform', 'selectListPlatform', search, limit
            )
        },
        getSelectListEvent(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/event', 'selectListEvent', search, limit
            )
        },
        getSelectListNews(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/news', 'selectListNews', search, limit
            )
        },
        getSelectListClient(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/client', 'selectListClient', search, limit
            )
        },
        getSelectListProjectType(search: string, limit: number) {
            return this.fetchSelectList<ISelectListDefault>('/api/select-list/project-type', 'selectListProjectType', search, limit
            )
        },
    },
})
