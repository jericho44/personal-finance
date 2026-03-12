import api from '@services/api';
import { defineStore } from 'pinia';
import { ICategoryPayload } from '@src/types/category';

export const useCategoryStore = defineStore('category', () => {

    async function getAll() {
        const response = await api().get('api/categories');
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

    return { getAll, getById, create, update, destroy };
});
