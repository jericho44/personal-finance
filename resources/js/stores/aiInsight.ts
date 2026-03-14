import { defineStore } from 'pinia';
import api from '@services/api';

interface AIInsightState {
    insight: string | null;
    loading: boolean;
    error: string | null;
}

export const useAIInsightStore = defineStore('aiInsight', {
    state: (): AIInsightState => ({
        insight: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchInsights(forceRefresh = false) {
            this.loading = true;
            this.error = null;

            try {
                if (forceRefresh) {
                    await api().delete('api/ai-insights/cache');
                }

                const response = await api().get('api/ai-insights');
                
                if (response.data.status === 'success') {
                    this.insight = response.data.content;
                } else {
                    this.error = response.data.message || 'Failed to get insights';
                }
            } catch (err: any) {
                this.error = err.response?.data?.message || 'An error occurred while fetching AI insights';
            } finally {
                this.loading = false;
            }
        },
    },
});
