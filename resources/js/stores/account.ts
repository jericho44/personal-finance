import api from '@services/api';
import { defineStore } from 'pinia';
import { IAccountPayload } from '@src/types/account';

export const useAccountStore = defineStore('account', () => {

    async function getAll() {
        const response = await api().get('api/accounts');
        return response;
    }

    async function getById(id: string) {
        const response = await api().get(`api/accounts/${id}`);
        return response;
    }

    async function create(payload: IAccountPayload) {
        const response = await api().post('api/accounts', payload);
        return response;
    }

    async function update(id: string, payload: IAccountPayload) {
        const response = await api().put(`api/accounts/${id}`, payload);
        return response;
    }

    async function destroy(id: string) {
        const response = await api().delete(`api/accounts/${id}`);
        return response;
    }

    return { getAll, getById, create, update, destroy };
});
