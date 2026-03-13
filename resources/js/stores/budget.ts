import api from '@services/api';
import { defineStore } from 'pinia';
import { IBudgetPayload } from '@src/types/budget';

export const useBudgetStore = defineStore('budget', () => {

    async function getAll(filters: Record<string, any> = {}) {
        const response = await api().get('api/budgets', {
            params: { ...filters, limit: 0 }
        });
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/budgets/${id}`);
        return response;
    }

    async function create(payload: IBudgetPayload) {
        const response = await api().post('api/budgets', payload);
        return response;
    }

    async function update(id: string, payload: IBudgetPayload) {
        const response = await api().put(`api/budgets/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/budgets/${id}`);
        return response;
    }

    async function getProgress(period: 'current_month' | 'last_month' = 'current_month') {
        const response = await api().get('api/budgets/progress', { params: { period } });
        return response;
    }

    return { getAll, getById, create, update, destroy, getProgress };
});
