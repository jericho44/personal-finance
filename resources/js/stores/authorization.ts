import api from '@services/api';
import { ILoginPayload, IUserProfile, IChangePasswordPayload, IRegisterPayload } from '@src/types/authorization';
import { defineStore } from 'pinia'
import { reactive } from 'vue';

export const useAuthorizationStore = defineStore('authorization', () => {
    const data = reactive({
        authorized: false,
        id: '',
        name: '',
        role: null,
    }) as IUserProfile;

    function setToken(token: string) {
        localStorage.setItem('lp_token', token)
    }

    function setProfileData(payload: IUserProfile | null) {
        data.authorized = payload ? true : false;
        data.id = payload?.id ?? '';
        data.name = payload?.name ?? '';
        data.role = payload?.role ?? null;
        data.telegram_id = payload?.telegram_id ?? null;
    }

    async function logout() {
        const res = await api().post('api/auth/logout')
        setToken('')
        localStorage.clear()
        setProfileData(null);
        return res;
    }

    async function getProfile(setToAuthorizationState = false) {
        try {
            const response = await api().get('api/auth/me');

            if (setToAuthorizationState) {
                setProfileData(response.data.data)
            }
            return response;
        } catch (error) {
            if (setToAuthorizationState) {
                setProfileData(null)
            }
            throw error;
        }
    }
    
    async function login(payload: ILoginPayload) {
        const response = await api().post('api/auth/login', payload);
        setToken(response.data.data.token.accessToken)
        return response;
    }

    async function register(payload: IRegisterPayload) {
        const response = await api().post('api/auth/register', payload);
        // Note: the template's register API may or may not return a token.
        // Assuming user needs to log in after registering.
        return response;
    }

    async function changePassword(payload: IChangePasswordPayload) {
        const response = await api().put('api/auth/change-password', payload);
        return response;
    }

    async function forgotPassword(payload: any) {
        const response = await api().post('api/auth/forgot-password', payload);
        return response;
    }

    async function resetPassword(payload: any) {
        const response = await api().post('api/auth/reset-password', payload);
        return response;
    }

    async function updateProfile(payload: any) {
        const response = await api().put('api/auth/me', payload);
        // Refresh profile state
        if (response.status === 200) {
            setProfileData(response.data.data);
        }
        return response;
    }

    async function enable2fa(payload: any) {
        const response = await api().post('api/auth/enable-google2fa', payload);
        return response;
    }

    async function get2faQrCode() {
        const response = await api().get('api/auth/qrcode-url-google2fa');
        return response;
    }

    async function generateTelegramCode() {
        const response = await api().post('api/auth/generate-telegram-code');
        return response;
    }

    return { data, setToken, logout, getProfile, login, register, changePassword, forgotPassword, resetPassword, updateProfile, enable2fa, get2faQrCode, generateTelegramCode };

});

