import ProfileIndex from "@src/views/admin/profile/Index.vue"
import Profile2FA from "@src/views/admin/profile/2FA.vue"

const routes = [
    {
        path: 'profile',
        children: [
            {
                path: '',
                name: 'a-profile',
                component: ProfileIndex,
            },
            {
                path: '2fa',
                name: 'a-profile-2fa',
                component: Profile2FA,
            },
        ]
    }
]

export default routes
