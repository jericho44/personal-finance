<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import { useAuthorizationStore } from '@stores/authorization';
import { loaderShow, loaderHide, axiosHandleError } from '@plugins/global';
import Swal from 'sweetalert2';
import useVuelidate from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import { ErrorFormValidation } from '@/components/main';
import { useRouter } from 'vue-router';

const router = useRouter();
const authorizationStore = useAuthorizationStore();

const qrCodeUrl = ref('');
const is2FaEnabled = ref(false);

const formData = reactive({
    code: ''
});

const rules = {
    code: { required }
};

const v$ = useVuelidate(rules, formData);

onMounted(async () => {
    try {
        loaderShow();
        const res = await authorizationStore.get2faQrCode();
        if (res.status === 200 && res.data.data) {
            qrCodeUrl.value = res.data.data.qrcode_url || '';
        }
    } catch (error) {
        // Suppress expected errors if 2FA is already enabled or handled differently
        console.error("Could not fetch QR Code", error);
    } finally {
        loaderHide();
    }
});

async function enable2Fa() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    try {
        loaderShow();
        const res = await authorizationStore.enable2fa(formData);
        if (res.status === 200) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Two-Factor Authentication telah diaktifkan.',
                icon: 'success',
                confirmButtonText: 'OK',
            }).then(() => {
                router.push({ name: 'a-profile' });
            });
        }
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Two-Factor Authentication (2FA)</h3>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted">Pindai QR Code di bawah menggunakan aplikasi Google Authenticator, Authy, atau aplikasi 2FA lainnya.</p>

                        <div v-if="qrCodeUrl" class="my-5 p-5 bg-light d-inline-block rounded">
                            <img :src="qrCodeUrl" alt="QR Code 2FA" style="width: 200px; height: 200px;" />
                        </div>
                        <div v-else class="my-5 p-5 bg-light d-inline-block rounded">
                            <span class="text-muted">Gagal memuat QR Code. Mungkin 2FA sudah aktif atau terjadi kesalahan.</span>
                        </div>

                        <div class="mt-5 text-start" style="max-width: 400px; margin: 0 auto;">
                            <div class="form-group mb-5">
                                <label class="form-label">Kode Verifikasi (6 digit)</label>
                                <input type="text" class="form-control text-center" v-model="formData.code" placeholder="Misal: 123456" maxlength="6">
                                <div class="text-center mt-2">
                                    <ErrorFormValidation :vfield="v$.code" />
                                </div>
                            </div>
                            
                            <button class="btn btn-primary w-100" @click="enable2Fa" :disabled="!qrCodeUrl && !formData.code">
                                Verifikasi dan Aktifkan
                            </button>
                        </div>

                        <div class="mt-10">
                            <router-link :to="{ name: 'a-profile' }" class="btn btn-light">Kembali ke Profil</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
