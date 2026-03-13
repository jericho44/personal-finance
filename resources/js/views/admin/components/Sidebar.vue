<template>
    <div>
        <!--begin::Aside-->
        <div id="kt_aside" class="aside aside-hoverable" 
            :class="themeStore.mode === 'light' ? 'aside-light' : 'aside-dark'"
            data-kt-drawer="true" data-kt-drawer-name="aside"
            data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
            data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
            data-kt-drawer-toggle="#kt_aside_mobile_toggle">
            <!--begin::Brand-->
            <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                <!--begin::Logo-->
                <LogoIcon />
                <!--end::Logo-->
                <!--begin::Aside toggler-->
                <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-white aside-toggle"
                    data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                    data-kt-toggle-name="aside-minimize">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
                    <span class="svg-icon svg-icon-1 rotate-180 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.5"
                                d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                                fill="black" />
                            <path
                                d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                                fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Aside toggler-->
            </div>
            <!--end::Brand-->
            <!--begin::Aside menu-->
            <div class="aside-menu flex-column-fluid">
                <!--begin::Aside Menu-->
                <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
                    data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                    data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
                    data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
                    <!--begin::Menu-->
                    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                        id="#kt_aside_menu" data-kt-menu="true">
                        <div class="menu-item py-1">
                            <div class="menu-content pb-2">
                                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Menu Utama</span>
                            </div>
                        </div>
                        <div class="menu-item py-1">
                            <router-link to="/admin/dashboard"
                                :class="`menu-link btn btn-sm mx-5 ${pageActive(['a-dashboard', 'a-index'])}`">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.4"
                                            d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2Z"
                                            fill="#B5B5C3" />
                                        <path
                                            d="M16.4198 7.81039V16.1904C16.4198 16.8304 15.8998 17.3504 15.2598 17.3504C14.6098 17.3504 14.0898 16.8304 14.0898 16.1904V7.81039C14.0898 7.17039 14.6098 6.65039 15.2598 6.65039C15.8998 6.65039 16.4198 7.17039 16.4198 7.81039Z"
                                            fill="#B5B5C3" />
                                        <path
                                            d="M9.91008 12.9295V16.1895C9.91008 16.8295 9.39008 17.3495 8.74008 17.3495C8.10008 17.3495 7.58008 16.8295 7.58008 16.1895V12.9295C7.58008 12.2895 8.10008 11.7695 8.74008 11.7695C9.39008 11.7695 9.91008 12.2895 9.91008 12.9295Z"
                                            fill="#A1A5B7" />
                                    </svg>

                                </span>
                                <span class="menu-title">Dashboard</span>
                            </router-link>
                            <!-- DATA MASTER SECTION -->
                            <div class="menu-item py-1"
                                v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])">
                                <div class="menu-content pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Data Master</span>
                                </div>
                            </div>
                            <div v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])"
                                data-kt-menu-trigger="click"
                                :class="`menu-item menu-accordion ${pageActive(['a-m-user', 'a-m-category', 'a-m-account']) == 'active' ? 'show' : ''}`">
                                <span class="menu-link toggle-accordion mx-2">
                                    <span class="menu-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M12.9336 16.0724L19.36 10.9564L19.5182 10.8312C20.1676 10.3169 20.2772 9.3735 19.7629 8.72404C19.6917 8.63411 19.6104 8.55264 19.5206 8.48124L12.9337 3.24252C12.3871 2.80783 11.6129 2.80783 11.0663 3.24252L4.47482 8.48483C3.82645 9.00049 3.71886 9.94413 4.23452 10.5925C4.305 10.6811 4.38527 10.7615 4.47382 10.832L4.62999 10.9564L11.0659 16.073C11.6127 16.5077 12.3871 16.5074 12.9336 16.0724Z"
                                                fill="#B5B5C3" />
                                            <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.0564 18.6709L5.33594 14.1231C4.94555 13.8127 4.37748 13.8776 4.06711 14.268C4.06473 14.271 4.06236 14.274 4.06001 14.277C3.75258 14.674 3.82518 15.245 4.22216 15.5525C4.2236 15.5536 4.22504 15.5547 4.22649 15.5558L11.0873 20.8033C11.6251 21.2146 12.3718 21.2147 12.9096 20.8035L19.7678 15.56C20.1693 15.253 20.246 14.6786 19.939 14.2771C19.9377 14.2753 19.9363 14.2736 19.935 14.2719C19.6267 13.8745 19.0546 13.8023 18.6572 14.1106C18.6544 14.1129 18.6515 14.1151 18.6486 14.1174L12.9235 18.6707C12.377 19.1053 11.6029 19.1054 11.0564 18.6709Z"
                                                fill="#B5B5C3" />
                                        </svg>
                                    </span>
                                    <span class="menu-title">Data Master</span>
                                    <span class="menu-arrow" />
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                     <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-category')}`"
                                            @click="$router.push({ name: 'a-m-category' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Kategori</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-user')}`"
                                            @click="$router.push({ name: 'a-m-user' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">User</span>
                                        </a>
                                    </div>
                                     <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-account')}`"
                                            @click="$router.push({ name: 'a-m-account' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Akun</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- TRANSAKSI SECTION -->
                            <div class="menu-item py-1"
                                v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])">
                                <div class="menu-content pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Keuangan</span>
                                </div>
                            </div>
                            <div v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])"
                                data-kt-menu-trigger="click"
                                :class="`menu-item menu-accordion ${pageActive(['a-m-transaction', 'a-m-budget', 'a-m-bill', 'a-m-goal', 'a-m-report']) == 'active' ? 'show' : ''}`">
                                <span class="menu-link toggle-accordion mx-2">
                                    <span class="menu-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3"
                                                d="M4 4H20V20H4V4Z"
                                                fill="#B5B5C3" />
                                            <path
                                                d="M7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z"
                                                fill="#B5B5C3" />
                                        </svg>
                                    </span>
                                    <span class="menu-title">Keuangan</span>
                                    <span class="menu-arrow" />
                                </span>
                                <div class="menu-sub menu-sub-accordion">

                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-transaction')}`"
                                            @click="$router.push({ name: 'a-m-transaction' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Transaksi</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-budget')}`"
                                            @click="$router.push({ name: 'a-m-budget' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Anggaran</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-bill')}`"
                                            @click="$router.push({ name: 'a-m-bill' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Tagihan & Langganan</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-goal')}`"
                                            @click="$router.push({ name: 'a-m-goal' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Tujuan Keuangan</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- REPORT -->
                            <div class="menu-item py-1"
                                v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])">
                                <div class="menu-content pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Report</span>
                                </div>
                            </div>
                             <div v-if="isSpecificHaveAccessMenu([environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin])"
                                data-kt-menu-trigger="click"
                                :class="`menu-item menu-accordion ${pageActive(['a-m-report']) == 'active' ? 'show' : ''}`">
                                <span class="menu-link toggle-accordion mx-2">
                                    <span class="menu-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3"
                                                d="M4 4H20V20H4V4Z"
                                                fill="#B5B5C3" />
                                            <path
                                                d="M7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z"
                                                fill="#B5B5C3" />
                                        </svg>
                                    </span>
                                    <span class="menu-title">Report</span>
                                    <span class="menu-arrow" />
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                    <div class="menu-item">
                                        <a :class="`menu-link btn mx-5 btn-sm ${pageActive('a-m-report')}`"
                                            @click="$router.push({ name: 'a-m-report' })" href="javascript:;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot" />
                                            </span>
                                            <span class="menu-title">Laporan Keuangan</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Aside Menu-->
            </div>
        </div>
        <!--end::Aside-->
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { LogoIcon } from '@/components/icons';
import { useAuthorizationStore } from '@/stores/authorization';
import { useEnvironmentStore } from '@/stores/environment';
import { useThemeStore } from '@/stores/theme';

const roleId = ref<string | null>(null);

const route = useRoute();
const authorizationStore = useAuthorizationStore();
const environmentStore = useEnvironmentStore();
const themeStore = useThemeStore();

const pageActive = (menu: string | string[]) => {
    let active = '';
    if (Array.isArray(menu)) {
        active = menu.includes(route.name as string) ? 'active' : '';
    } else {
        active = route.name === menu ? 'active' : '';
    }
    return active;
};

const isSpecificHaveAccessMenu = (role: string[]) => {
    let response = false;
    if (
        role.filter((e) => e == authorizationStore.data.role?.slug).length > 0
    ) {
        response = true;
    }

    return response;
}


onMounted(() => {
    roleId.value = localStorage.getItem('rId');
});
</script>


<style scoped>
.menu-state-title-primary .menu-item.hover:not(.here)>.menu-link:not(.disabled):not(.active):not(.here) .menu-title,
.menu-state-title-primary .menu-item:not(.here) .menu-link:hover:not(.disabled):not(.active):not(.here) .menu-title {
    color: #148FFF !important;
}

.menu-item .active {
    background-color: #148FFF !important;
    color: white !important;
}

.menu-item .active * {
    color: white !important;
}

.menu-item .active .menu-bullet span {
    background-color: white !important;
}

.menu-item .active .menu-icon svg path {
    background-color: white !important;
    fill: white !important;
}

.dropdown-menu {
    width: 100%;
    box-shadow: none;
}

/* Style untuk panah ke samping saat dropdown pasif */
.rotate-right {
    transform: rotate(-90deg);
    /* Panah mengarah ke samping */
    transition: transform 0.3s ease;
}
</style>
