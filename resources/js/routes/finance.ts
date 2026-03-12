import CategoryIndex from "@src/views/admin/category/Index.vue"
import AccountIndex from "@src/views/admin/account/Index.vue"
import TransactionIndex from "@src/views/admin/transaction/Index.vue"
import BudgetIndex from "@src/views/admin/budget/Index.vue"

const routes = [
    {
        path: 'categories',
        name: 'a-categories',
        component: CategoryIndex,
    },
    {
        path: 'accounts',
        name: 'a-accounts',
        component: AccountIndex,
    },
    {
        path: 'transactions',
        name: 'a-transactions',
        component: TransactionIndex,
    },
    {
        path: 'budgets',
        name: 'a-budgets',
        component: BudgetIndex,
    }
]

export default routes
