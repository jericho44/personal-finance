import { RouteRecordRaw } from "vue-router";
import AIInsights from "@/views/admin/ai/Insights.vue";

const routes: Array<RouteRecordRaw> = [
    {
        path: 'ai-insights',
        name: 'a-ai-insights',
        component: AIInsights,
        meta: {
            auth: true,
        },
    },
];

export default routes;
