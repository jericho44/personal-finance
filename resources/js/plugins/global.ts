import moment from 'moment';
import Swal from 'sweetalert2';
import axios, { AxiosInstance } from 'axios';
import router from '@src/routes';

declare const assetUrl: string;
declare const apiUrl: string;

declare const KTDialer: {
    init: () => void;
    destroy: () => void;
};
declare const KTImageInput: {
    init: () => void;
    destroy: () => void;
};
declare const KTDrawer: {
    init: () => void;
    destroy: () => void;
};
declare const KTMenu: {
    init: () => void;
    createInstances: () => void;
};
declare const KTPasswordMeter: {
    init: () => void;
    destroy: () => void;
};
declare const KTScroll: {
    init: () => void;
    destroy: () => void;
};
declare const KTScrolltop: {
    init: () => void;
    destroy: () => void;
};
declare const KTSticky: {
    init: () => void;
    destroy: () => void;
};
declare const KTSwapper: {
    init: () => void;
    destroy: () => void;
};
declare const KTToggle: {
    init: () => void;
    destroy: () => void;
};
declare const KTUtil: {
    onDOMContentLoaded: (callback: () => void) => void;
};
declare const KTApp: {
    init: () => void;
    initPageLoader: () => void;
};
declare const KTLayoutAside: {
    init: () => void;
    destroy: () => void;
};
declare const KTLayoutSearch: {
    init: () => void;
    destroy: () => void;
};
declare const KTLayoutToolbar: {
    init: () => void;
    destroy: () => void;
};

interface AxiosErrorResponse {
    response?: {
        status?: number;
        data?: {
            meta?: {
                message?: string | Record<string, string[]>;
            };
            status?: {
                message?: string;
            };
            data?: {
                errors?: Record<string, { message: string }>;
            };
        };
    };
}


export function formatBytes(bytes: number, decimals = 2): string {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

export function noNullable(value: string | null | undefined): string {
    return value || '-';
}

export function rupiahFormat(value: number | string): string {
    if (!value) return '0';
    return new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 5,
    }).format(Number(value));
}

export function prefix(type: string): string {
    return '/' + type;
}

export function getAssetUrl(): string {
    return assetUrl;
}

export function getToken(): string | null {
    return localStorage.getItem('lp_token');
}

export function setNoImageUrl(event: Event): void {
    const target = event.target as HTMLImageElement;
    target.src = `${assetUrl}extends/images/noimage.png`;
}

export function truncateText(text: string, limit: number): string {
    if (!text) return '';
    return text.length > limit ? text.slice(0, limit) + '...' : text;
}

export function validatorForbiddenHtml(value: string) {
    const isHtml = (str: string): boolean =>
        /<\/?[a-z][\s\S]*>/i.test(str) || /<script|style|iframe/i.test(str);

    if (!value || !isHtml(value)) {
        return { $valid: true };
    }

    return {
        $valid: false,
        message: 'Tidak boleh mengandung text HTML,CSS,Javascript',
    };
}

export function loaderShow(): void {
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'ewp-loading';
    document.body.appendChild(loadingDiv);
}

export function loaderHide(): void {
    $('.ewp-loading').hide();
}

export function axiosInstance(isFormData = false): AxiosInstance {
    const token = localStorage.getItem('travel_token');
    const headers = {
        Authorization: token ? `Bearer ${token}` : '',
        'Content-Type': isFormData ? 'multipart/form-data' : 'application/json',
    };

    const instance = axios.create({
        baseURL: apiUrl,
        headers,
    });

    instance.interceptors.request.use((config) => config);
    instance.interceptors.response.use((response) => response);

    return instance;
}

export function initializeAppPlugins(): void {
    $(".drawer-overlay").remove();

    setTimeout(() => {
        KTDialer?.init();
        KTDrawer?.init();
        KTImageInput?.init();
        KTMenu?.createInstances();
        KTPasswordMeter?.init();
        KTScroll?.init();
        KTScrolltop?.init();
        KTSticky?.init();
        KTSwapper?.init();
        KTToggle?.init();
        KTUtil?.onDOMContentLoaded(() => KTApp?.init());
        window.addEventListener("load", () => KTApp?.initPageLoader());

        KTUtil?.onDOMContentLoaded(() => {
            KTLayoutAside?.init();
            KTLayoutSearch?.init();
            KTLayoutToolbar?.init();
        });
    }, 100);

    setTimeout(() => {
        $('body').attr('data-kt-drawer-aside', 'off');
        $('body').attr('data-kt-drawer', 'off');
        $('body').attr('data-kt-aside-minimize', 'off');
        $(".drawer-overlay").remove();
    }, 10);

    $("#kt_aside_mobile_toggle").on('click', () => {
        setTimeout(() => {
            const overlays = document.querySelectorAll('.drawer-overlay');

            if (overlays.length > 1) {
                overlays.forEach((el) => {
                    el.remove();
                });
            }

        }, 10);
    });
}

export function axiosHandleError(error: unknown): void {
    const err = error as AxiosErrorResponse;
    const data = err?.response?.data;
    const statusCode = Number(err?.response?.status);
    let message = 'Terjadi kesalahan';
    let title = 'OOPS...';
    let icon: 'error' | 'warning' = 'error';

    switch (statusCode) {
        case 400:
            title = 'Bad Request';
            if (typeof data?.meta?.message === 'object') {
                message = '';
                for (const key in data.meta.message) {
                    for (const val of data.meta.message[key]) {
                        message += `${val}<br>`;
                    }
                }
            } else {
                message = data?.meta?.message || 'Permintaan tidak valid';
            }
            icon = 'warning';
            break;
        case 401:
            title = 'Unauthorized';
            message = 'Silahkan login kembali';
            icon = 'warning';
            break;
        case 403:
            title = 'Forbidden';
            message = data?.status?.message || 'Akses ditolak';
            icon = 'warning';
            break;
        case 404:
            title = 'URL Not Found';
            message = data?.status?.message || 'Halaman yang diminta tidak ditemukan';
            icon = 'warning';
            break;
        case 422:
            title = 'Pastikan data sudah benar!';
            if (typeof data?.data?.errors === 'object') {
                message = Object.values(data.data.errors)
                    .map((err) => err.message)
                    .join('<br>');
            } else {
                message = typeof data?.meta?.message === 'string' ? data.meta.message : '';
            }
            icon = 'warning';
            break;
        default:
            title = 'Terjadi kesalahan koneksi';
            icon = 'error';
    }

    Swal.fire({ title, html: message, icon }).then(() => {
        if (statusCode === 401) {
            localStorage.clear()
            $('.modal').modal('hide');
            router.replace({ name: 'login' });
        }
    });
}

export const $moment = moment;

export function getImageUrl(url: string | null): string {
    return `${url}?token=${getToken()}`;
}
