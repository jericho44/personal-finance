import api from '@services/api';
import { defineStore } from 'pinia';

export const useReportStore = defineStore('report', () => {

    async function getMonthly(year: string, month: string) {
        const response = await api().get('api/reports/monthly', { params: { year, month } });
        return response;
    }

    async function getYearly(year: string) {
        const response = await api().get('api/reports/yearly', { params: { year } });
        return response;
    }

    async function getCategoryExpense(year: string, month: string) {
        const response = await api().get('api/reports/category-expense', { params: { year, month } });
        return response;
    }

    return { getMonthly, getYearly, getCategoryExpense };
});
