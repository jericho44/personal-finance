import api from '@services/api';
import { defineStore } from 'pinia';
import { IGoalPayload, IGoalFilters } from '@src/types/goal';

export const useGoalStore = defineStore('goal', () => {

    async function getAll(filters: IGoalFilters = {}) {
        const response = await api().get('api/goals', { params: { ...filters, limit: 0 } });
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/goals/${id}`);
        return response;
    }

    async function create(payload: IGoalPayload) {
        const response = await api().post('api/goals', payload);
        return response;
    }

    async function update(id: string, payload: IGoalPayload) {
        const response = await api().put(`api/goals/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/goals/${id}`);
        return response;
    }

    return { getAll, getById, create, update, destroy };
});
