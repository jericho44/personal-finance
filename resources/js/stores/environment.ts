import { defineStore } from 'pinia'
import {
    reactive
} from 'vue';
import {
    env
} from '@src/utils/env';

interface EnvironmentState {
    appURL: string,
    apiURL: string,
    maxFileUploadSize: number, // in MB
    defaultPassword: string,
    subDirectory: string,

    roleSuperAdmin: string,
    roleDeveloper: string,
    roleAdmin: string,

}

export const useEnvironmentStore = defineStore('environment', () => {
    const data = reactive({
        appURL: env('VITE_APP_URL', 'http://127.0.0.1:8000/'),
        apiURL: env('VITE_API_URL', 'http://127.0.0.1:8000/'),
        maxFileUploadSize: env('VITE_MAX_FILE_UPLOAD_SIZE', 10), // in MB
        defaultPassword: env('VITE_DEFAULT_PASSWORD', '123456'),
        subDirectory: env('VITE_SUBDIRECTORY', '/'),
        roleSuperAdmin: env('VITE_ROLE_SUPER_ADMIN', 'superadmin'),
        roleDeveloper: env('VITE_ROLE_DEVELOPER', 'developer'),
        roleAdmin: env('VITE_ROLE_ADMIN', 'admin'),
    }) as EnvironmentState;
    return {
        data,
    };

});
