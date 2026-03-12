import api from '@services/api';
import { defineStore } from 'pinia';
import { ITransactionPayload, ITransactionFilters } from '@src/types/transaction';

export const useTransactionStore = defineStore('transaction', () => {

    async function getAll(filters: ITransactionFilters = {}) {
        const response = await api().get('api/transactions', { params: filters });
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/transactions/${id}`);
        return response;
    }

    async function create(payload: ITransactionPayload) {
        const response = await api().post('api/transactions', payload);
        return response;
    }

    async function update(id: string, payload: ITransactionPayload) {
        const response = await api().put(`api/transactions/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/transactions/${id}`);
        return response;
    }

    return { getAll, getById, create, update, destroy };
});
