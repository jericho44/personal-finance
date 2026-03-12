import { defineStore } from 'pinia';
import Api from '@/services/api';
import { IDashboardResponse } from '@/types/dashboard';

export const useDashboardStore = defineStore('dashboard', {
    actions: {
        async fetchSummary() {
            const response = await Api().get<IDashboardResponse>('api/dashboard');
            return response;
        },
    },
});
