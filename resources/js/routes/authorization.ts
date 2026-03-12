import Login from "@src/views/Login.vue"
import Register from "@src/views/Register.vue"
import ForgotPassword from "@src/views/ForgotPassword.vue"
import ResetPassword from "@src/views/ResetPassword.vue"

const routes = [
    {
        path: '/',
        name: 'login',
        component: Login,
        meta: {
            auth: false,
            guest: true
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            auth: false,
            guest: true
        }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword,
        meta: {
            auth: false,
            guest: true
        }
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPassword,
        meta: {
            auth: false,
            guest: true
        }
    }
]

export default routes
