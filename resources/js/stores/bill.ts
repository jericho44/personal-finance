import api from '@services/api';
import { defineStore } from 'pinia';
import { IBillPayload, IBillFilters } from '@src/types/bill';

export const useBillStore = defineStore('bill', () => {

    async function getAll(filters: IBillFilters = {}) {
        const response = await api().get('api/bills', { params: { ...filters, limit: 0 } });
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/bills/${id}`);
        return response;
    }

    async function create(payload: IBillPayload) {
        const response = await api().post('api/bills', payload);
        return response;
    }

    async function update(id: string, payload: IBillPayload) {
        const response = await api().put(`api/bills/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/bills/${id}`);
        return response;
    }

    return { getAll, getById, create, update, destroy };
});
