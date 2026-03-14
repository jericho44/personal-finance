<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useAuthorizationStore } from '@stores/authorization';
import useVuelidate from '@vuelidate/core';
import { required, minLength, sameAs } from '@vuelidate/validators';
import { ErrorFormValidation } from '@/components/main';
import { loaderShow, loaderHide, axiosHandleError } from '@plugins/global';
import Swal from 'sweetalert2';

const authorizationStore = useAuthorizationStore();

const profileData = reactive({
    name: authorizationStore.data.name || '',
});

const profileRules = {
    name: { required },
};

const vProfile$ = useVuelidate(profileRules, profileData);

const passwordData = reactive({
    currentPassword: '',
    newPassword: '',
    newPasswordConfirmation: '',
});

const passwordRules = {
    currentPassword: { required },
    newPassword: { required, minLength: minLength(6) },
    newPasswordConfirmation: { required, sameAsPassword: sameAs(passwordData.newPassword) },
};

const vPassword$ = useVuelidate(passwordRules, passwordData);

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showNewPasswordConfirmation = ref(false);

onMounted(() => {
    // Sync if not already set
    if (!profileData.name) {
        profileData.name = authorizationStore.data.name;
    }
});

async function updateProfile() {
    vProfile$.value.$touch();
    if (vProfile$.value.$invalid) return;

    try {
        loaderShow();
        const res = await authorizationStore.updateProfile({ name: profileData.name });
        if (res.status === 200) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Profil berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK',
            });
        }
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function changePassword() {
    vPassword$.value.$touch();
    if (vPassword$.value.$invalid) return;

    try {
        loaderShow();
        const res = await authorizationStore.changePassword(passwordData);
        if (res.status === 200) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Password berhasil diubah.',
                icon: 'success',
                confirmButtonText: 'OK',
            });
            // Reset form
            passwordData.currentPassword = '';
            passwordData.newPassword = '';
            passwordData.newPasswordConfirmation = '';
            vPassword$.value.$reset();
        }
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

const telegramLinkCode = ref('');
const botUsername = ref('');

async function generateTelegramCode() {
    try {
        loaderShow();
        const res = await authorizationStore.generateTelegramCode();
        if (res.status === 200) {
            telegramLinkCode.value = res.data.data.link_code;
            botUsername.value = res.data.data.bot_username;
        }
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

function openTelegram() {
    window.open(`https://t.me/${botUsername.value}?start=${telegramLinkCode.value}`, '_blank');
}
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Profil Pengguna</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Profile Update Form -->
                        <div class="row mb-10">
                            <div class="col-md-6 border-end pe-md-10">
                                <h4 class="mb-5">Informasi Dasar</h4>
                                <div class="form-group mb-5">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" v-model="profileData.name" placeholder="Nama Lengkap">
                                    <ErrorFormValidation :vfield="vProfile$.name" />
                                </div>
                                <button class="btn btn-primary" @click="updateProfile">Simpan Profil</button>
                            </div>

                            <!-- Change Password Form -->
                            <div class="col-md-6 ps-md-10">
                                <h4 class="mb-5">Ubah Password</h4>
                                <div class="form-group mb-5">
                                    <label class="form-label">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input :type="showCurrentPassword ? 'text' : 'password'" class="form-control" v-model="passwordData.currentPassword" placeholder="Password Saat Ini" style="border-right: 0 !important;">
                                        <div class="input-group-text cursor-pointer bg-white" @click="showCurrentPassword = !showCurrentPassword">
                                            <i :class="showCurrentPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" />
                                        </div>
                                    </div>
                                    <ErrorFormValidation :vfield="vPassword$.currentPassword" />
                                </div>

                                <div class="form-group mb-5">
                                    <label class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input :type="showNewPassword ? 'text' : 'password'" class="form-control" v-model="passwordData.newPassword" placeholder="Password Baru" style="border-right: 0 !important;">
                                        <div class="input-group-text cursor-pointer bg-white" @click="showNewPassword = !showNewPassword">
                                            <i :class="showNewPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" />
                                        </div>
                                    </div>
                                    <ErrorFormValidation :vfield="vPassword$.newPassword" />
                                </div>

                                <div class="form-group mb-5">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input :type="showNewPasswordConfirmation ? 'text' : 'password'" class="form-control" v-model="passwordData.newPasswordConfirmation" placeholder="Konfirmasi Password Baru" style="border-right: 0 !important;">
                                        <div class="input-group-text cursor-pointer bg-white" @click="showNewPasswordConfirmation = !showNewPasswordConfirmation">
                                            <i :class="showNewPasswordConfirmation ? 'fa fa-eye-slash' : 'fa fa-eye'" />
                                        </div>
                                    </div>
                                    <ErrorFormValidation :vfield="vPassword$.newPasswordConfirmation" />
                                </div>

                                <button class="btn btn-primary" @click="changePassword">Ubah Password</button>
                            </div>
                        </div>

                        <hr />

                        <!-- Telegram Integration -->
                        <div class="row mt-10">
                            <div class="col-12 text-center">
                                <h4 class="mb-5">Integrasi Telegram</h4>
                                <div v-if="authorizationStore.data.telegram_id" class="alert alert-success d-inline-block">
                                    <i class="fa fa-check-circle me-2 text-success"></i>
                                    Akun Anda sudah terhubung dengan Telegram.
                                </div>
                                <div v-else>
                                    <p class="text-muted">Hubungkan akun Anda dengan Telegram untuk mencatat transaksi lebih cepat melalui chat.</p>
                                    
                                    <div v-if="telegramLinkCode" class="mt-5">
                                        <div class="alert alert-info d-inline-block mb-3">
                                            Kode Anda: <strong>{{ telegramLinkCode }}</strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-success" @click="openTelegram">
                                                <i class="fab fa-telegram me-2"></i> Buka Telegram & Hubungkan
                                            </button>
                                        </div>
                                        <p class="small text-muted mt-2">Atau kirim <code>/start {{ telegramLinkCode }}</code> ke @{{ botUsername }}</p>
                                    </div>
                                    <button v-else class="btn btn-outline btn-outline-info mt-3" @click="generateTelegramCode">
                                        <i class="fab fa-telegram me-2"></i> Hubungkan Telegram
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <!-- 2FA Link -->
                        <div class="row mt-10">
                            <div class="col-12 text-center">
                                <h4>Keamanan Tambahan</h4>
                                <p class="text-muted">Gunakan Two-Factor Authentication (2FA) untuk meningkatkan keamanan akun Anda.</p>
                                <router-link :to="{ name: 'a-profile-2fa' }" class="btn btn-outline btn-outline-primary mt-3">
                                    Atur Two-Factor Authentication
                                </router-link>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
