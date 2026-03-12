<template>
    <div>
        <!--begin::Header-->
        <div id="kt_header" class="header align-items-stretch"
            style="background-color: white; box-shadow: 0px 1px 5px rgba(0,0,0,0.1);">
            <!--begin::Container-->
            <div class="container-fluid d-flex align-items-stretch justify-content-between">
                <!--begin::Aside mobile toggle-->
                <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
                    <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                        id="kt_aside_mobile_toggle">
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                        <span class="svg-icon svg-icon-2x mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                    fill="black" />
                                <path opacity="0.3"
                                    d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                </div>
                <!--end::Aside mobile toggle-->
                <!--begin::Mobile logo-->
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                    <!-- <a href="../../demo1/dist/index.html" class="d-lg-none">
                        <img alt="Logo" :src="`${assetUrl}assets/images/logo_etibi.svg`" class="h-30px" />
                    </a> -->
                </div>
                <!--end::Mobile logo-->
                <!--begin::Wrapper-->
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                    <!--begin::Navbar-->
                    <div class="d-flex align-items-stretch" id="kt_header_nav">
                        <!--begin::Menu wrapper-->
                        <div class="header-menu align-items-stretch" data-kt-drawer="true"
                            data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                            data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
                            data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle"
                            data-kt-swapper="true" data-kt-swapper-mode="prepend"
                            data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                            <!--begin::Menu-->
                            <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                                id="#kt_header_menu" data-kt-menu="true">
                                <div class="menu-item me-lg-1">
                                    <div class="title-navbar d-flex align-items-center">
                                        <!-- <img :src="`${assetUrl}assets/images/logo_jawa_timur.svg`" class="me-3" alt=""> -->
                                        <!-- <div class="subtitle">
                                            <h4 class="mb-0">E-TIBI</h4>
                                            <small class="text-gray-700">Skrining Mandiri Gejala TBC</small>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::Navbar-->
                    <!--begin::Topbar-->
                    <div class="d-flex align-items-stretch flex-shrink-0">
                        <!--begin::Toolbar wrapper-->
                        <div class="d-flex align-items-stretch flex-shrink-0">
                            <!--begin::Search-->
                            <div class="d-flex align-items-stretch ms-1 ms-lg-3">
                                <!--begin::User-->
                                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                    <!--begin::Menu wrapper-->
                                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px"
                                        data-kt-menu-trigger="click" data-kt-menu-attach="parent"
                                        data-kt-menu-placement="bottom-end">
                                        <button class="btn btn-light-red btn-sm"><span
                                                style="font-weight:bold; font-size:1.3em;">A</span></button>
                                    </div>
                                    <!--begin::Menu-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                                        data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-50px me-5">
                                                    <button class="btn btn-light-red btn-sm"><span
                                                            style="font-weight:bold; font-size:1.3em;">A</span></button>
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::Username-->
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bolder d-flex align-items-center fs-5"> {{
                                                        authorizationStore.data.name }}</div>
                                                </div>
                                                <!--end::Username-->
                                            </div>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu separator-->
                                        <div class="separator my-2" />
                                        <!--end::Menu separator-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="javascript:;" @click="showModalResetPassword()"
                                                class="menu-link px-5">Ubah Password</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="javascript:;" @click="logout()"
                                                class="menu-link px-5 text-danger">Keluar</a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                    <!--end::Menu wrapper-->
                                </div>
                                <!--end::User -->
                            </div>
                            <!--end::Toolbar wrapper-->
                        </div>
                        <!--end::Topbar-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Container-->
            </div>
        </div>
        <!--end::Header-->

        <div class="modal fade" tabindex="-1" id="modal-navbar-profile">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <h3 class="modal-title">Ubah Password</h3>
                                <span class="text-muted">Pastikan untuk selalu mengingat password terbaru anda</span>
                            </div>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close" />
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="form-group-pass" style="position:relative;">
                            <label for="password-now">
                                <h5>Password Lama</h5>
                            </label>
                            <input v-model="form.current.value"
                                :type="form.current.hiddenPassword ? 'password' : 'text'" class="form-control"
                                placeholder="Masukkan password">
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-fill fa-2x" v-if="form.current.hiddenPassword"
                                @click="showHidePassword(false, 0)" />
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-slash-fill fa-2x" v-else @click="showHidePassword(true, 0)" />
                        </div>
                        <div class="form-group-pass my-4" style="position:relative;">
                            <label for="password-now">
                                <h5>Password Baru</h5>
                            </label>
                            <input v-model="form.new.value" :type="form.new.hiddenPassword ? 'password' : 'text'"
                                class="form-control" placeholder="Masukkan password">
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-fill fa-2x" v-if="form.new.hiddenPassword"
                                @click="showHidePassword(false, 1)" />
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-slash-fill fa-2x" v-else @click="showHidePassword(true, 1)" />
                        </div>
                        <div class="form-group-pass my-4" style="position:relative;">
                            <label for="password-now">
                                <h5>Konfirmasi Password</h5>
                            </label>
                            <input v-model="form.confirm.value"
                                :type="form.confirm.hiddenPassword ? 'password' : 'text'" class="form-control"
                                placeholder="Masukkan password">
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-fill fa-2x" v-if="form.confirm.hiddenPassword"
                                @click="showHidePassword(false, 2)" />
                            <i style="position:absolute; right:10px; bottom:7px; cursor:pointer;"
                                class="px-4 bi bi-eye-slash-fill fa-2x" v-else @click="showHidePassword(true, 2)" />
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-red" @click="simpanPassword()">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';
import Swal from 'sweetalert2';

import { useAuthorizationStore } from '@/stores/authorization';
import { axiosHandleError, loaderHide, loaderShow } from '@/plugins/global';

const authorizationStore = useAuthorizationStore()

const router = useRouter();

const form = reactive({
    current: {
        hiddenPassword: true,
        value: '',
    },
    new: {
        hiddenPassword: true,
        value: '',
    },
    confirm: {
        hiddenPassword: true,
        value: '',
    },
});

function showHidePassword(action: boolean, type: number) {
    if (type === 0) {
        form.current.hiddenPassword = action;
    } else if (type === 1) {
        form.new.hiddenPassword = action;
    } else {
        form.confirm.hiddenPassword = action;
    }
}

async function logout() {
    try {
        loaderShow()
        const res = await authorizationStore.logout();
        Swal.fire('Berhasil', res?.data?.meta?.message || 'Berhasil keluar dari aplikasi', 'success')
            .then(() => {
                setTimeout(() => {
                    router.push({ name: 'login' });
                }, 300);
            });

    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide()
    }
}

function showModalResetPassword() {
    form.current.value = '';
    form.new.value = '';
    form.confirm.value = '';
    form.current.hiddenPassword = true;
    form.new.hiddenPassword = true;
    form.confirm.hiddenPassword = true;

    $('#modal-navbar-profile').modal('show');
}

async function simpanPassword() {
    const data = {
        currentPassword: form.current.value,
        newPassword: form.new.value,
        newPasswordConfirmation: form.confirm.value,
    };

    if (!data.currentPassword || !data.newPassword || !data.newPasswordConfirmation) {
        toast.error('Semua field harus terisi!');
        return;
    }
    if (data.newPassword.length < 6) {
        toast.error('Minimal password adalah 6 karakter!');
        return;
    }
    if (data.newPassword !== data.newPasswordConfirmation) {
        toast.error('Konfirmasi password tidak sesuai!');
        return;
    }

    try {
        loaderShow();
        const res = await authorizationStore.changePassword(data);
        $('.modal').modal('hide');
        Swal.fire('Berhasil', res?.data?.meta?.message || 'Berhasil mengubah password', 'success');
    } catch (err) {
        axiosHandleError(err)
    } finally {
        loaderHide()
    }
}
</script>
