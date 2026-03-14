
import { createRouter, createWebHistory } from 'vue-router'
import Admin from "@/views/admin/Index.vue";

import Dashboard from "@src/views/admin/dashboard/Index.vue"

// errors
import NotFound from "@/views/errors/404.vue";
import ForbiddenAccess from "@/views/errors/403.vue";


import masterRoutes from "@routes/master";
import authRoutes from "@routes/authorization";
import profileRoutes from "@routes/profile";
import financeRoutes from "@routes/finance";
import aiRoutes from "@routes/ai";

import { useAuthorizationStore } from '@src/stores/authorization';

const routes = [
    ...authRoutes,
    {
        path: '/admin',
        component: Admin,
        meta: {
            auth: true,
        },
        children: [
            {
                path: '',
                name: 'a-index',
                component: Dashboard
            },
            {
                path: 'dashboard',
                name: 'a-dashboard',
                component: Dashboard
            },
            ...profileRoutes,
            ...financeRoutes,
            ...aiRoutes,
            ...masterRoutes,
        ]
    },

    {
        path: "/404",
        component: NotFound,
        name: "not-found",
    },
    {
        path: "/403",
        component: ForbiddenAccess,
        name: "forbidden",
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },

]

const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_SUBDIRECTORY),
    routes,
    scrollBehavior() {
        return {
            top: 0,
        };
    },
});

router.beforeEach((to, _from, next) => {
    const authorizationStore = useAuthorizationStore();
    const token = localStorage.getItem("lp_token");

    if (to.matched.some((record) => record.meta.auth)) {
        if (!token) {
            return next({
                name: "login",
            });
        }

        const checkRoleAccess = () => {
            if (to.matched.some((record) => record.meta.specificRole)) {
                if (
                    to.meta &&
                    Array.isArray(to.meta.specificRole) &&
                    to.meta.specificRole.length > 0
                ) {
                    const hasAccess = to.meta.specificRole.filter(
                        (e: string) => e == authorizationStore.data.role?.slug
                    );
                    if (hasAccess.length == 0) {
                        return next("/403");
                    }
                }
            }
            return next();
        };

        if (authorizationStore.data.authorized === true) {
            return checkRoleAccess();
        }

        if (authorizationStore.data.authorized === false) {
            authorizationStore.getProfile(true).then(() => {
                return checkRoleAccess();
            }).catch(() => {
                localStorage.clear();
                return next({
                    name: "login",
                });
            });
        }
    } else if (to.matched.some((record) => record.meta.guest)) {
        // Cek jika route untuk guest (tanpa login)
        if (!token) {
            return next(); // Lanjutkan jika belum login
        } else {
            return next({ name: "a-dashboard" }); // Jika sudah login, redirect ke halaman dashboard
        }
    } else {
        // Jika rute tidak membutuhkan autentikasi, lanjutkan
        return next();
    }
})


export default router
