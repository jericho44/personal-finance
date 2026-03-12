import MasterCategory from "@src/views/admin/category/Index.vue"
import Transaction from "@src/views/admin/transaction/Index.vue"
import Budget from "@src/views/admin/budget/Index.vue"
import Account from "@src/views/admin/account/Index.vue"
import MasterUser from "@src/views/admin/master/user/Index.vue"
import MasterKategoriBerita from "@src/views/admin/master/kategori-berita/Index.vue"

import { useEnvironmentStore } from "@/stores/environment"
import { createPinia, setActivePinia } from "pinia"

const pinia = createPinia()
setActivePinia(pinia)

const environmentStore = useEnvironmentStore();

const routes = [
    {
        path: 'master',
        children: [
            // {
            //     path: 'kategori-berita',
            //     name: 'a-m-kategori-berita',
            //     component: MasterKategoriBerita,
            //     meta: {
            //         specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
            //     }
            // },
             {
                path: 'category',
                name: 'a-m-category',
                component: MasterCategory,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'user',
                name: 'a-m-user',
                component: MasterUser,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'transaction',
                name: 'a-m-transaction',
                component: Transaction,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'budget',
                name: 'a-m-budget',
                component: Budget,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'account',
                name: 'a-m-account',
                component: Account,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
        ]
    }
]

export default routes
