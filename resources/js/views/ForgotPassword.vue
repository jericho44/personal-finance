<script setup lang="ts">
import { useAuthorizationStore } from "@stores/authorization";
import { reactive, ref, onMounted } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";

import { ErrorFormValidation } from "@/components/main";
import { AxiosResponse } from "axios";
import { useRouter } from "vue-router";
import { initializeAppPlugins, loaderShow, loaderHide, getAssetUrl, axiosHandleError } from '@plugins/global';
import { RectangleIcon } from "@/components/icons";

const router = useRouter();
const authorizationStore = useAuthorizationStore();

const bgLogin = ref(getAssetUrl() + 'extends/images/bg-login.png');
const disabledButton = ref(false);
const successMessage = ref("");

const single = reactive({
    email: "",
});

const rules = {
    email: { required, email },
};

const v$ = useVuelidate(rules, single);

onMounted(() => {
    initializeAppPlugins();
});

async function requestReset() {
    try {
        v$.value.$touch();
        if (v$.value.$invalid) return;
        if (disabledButton.value) return false;

        disabledButton.value = true;
        loaderShow();

        const response = await authorizationStore.forgotPassword(single) as AxiosResponse;
        if (response && response.status === 200) {
            successMessage.value = "Tautan reset password telah dikirim ke email Anda.";
        }
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
        disabledButton.value = false;
    }
}
</script>

<template>
    <div class="row m-0 p-0" style="background-color: white; height:100dvh; width:100%; overflow-x: hidden;">
        <div class="col-12 col-lg-7" style="height:100%;">
            <div class="container d-flex align-items-center justify-content-center pt-10" style="height:100%;">
                <div class="d-flex align-items-center justify-content-center w-100 h-100" style="position: relative; z-index: 3;">
                    <div class="card w-100" style="max-width: 500px; border-radius: 14px; background-color: #f4f4f4; border: 1px solid #D9DBE4;">
                        <div class="card-body d-flex flex-column gap-5" style="font-size: 1.5rem;">
                            <div class="text-center mt-5">
                                <h3 class="mb-2" style="font-size: 2.5rem;">Lupa Password</h3>
                                <div style="color: #7E8299; font-size: 1.35rem;">Masukkan email Anda untuk reset password</div>
                            </div>
                            
                            <div v-if="successMessage" class="alert alert-success mt-3" style="font-size: 1.35rem;">
                                {{ successMessage }}
                            </div>

                            <div class="form-group mt-5">
                                <label class="form-label" :class="{ 'text-danger': v$.email.$error }" style="color: #3F4254; font-size: 1.35rem;">Email</label>
                                <div class="input-group">
                                    <input type="email" v-model="single.email" class="form-control" style="font-size: 1.35rem; height: 55px;" placeholder="Masukan email Anda">
                                </div>
                                <ErrorFormValidation :vfield="v$.email" />
                            </div>

                            <button @click="requestReset" class="btn btn-primary w-100 mt-5" style="font-size: 1.5rem; height: 55px;">Kirim Link Reset</button>

                            <div class="text-center mt-3 mb-5" style="font-size: 1.35rem; color: #7E8299;">
                                Kembali ke 
                                <router-link :to="{name: 'login'}" class="text-primary fw-bolder">Masuk</router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5 position-relative d-md-none d-lg-block" style="height: 100%; background-color: #2E5BEE; overflow: hidden;">
            <div class="position-absolute d-flex justify-content-between w-100 px-10" style="top: 0; z-index: 1;"><RectangleIcon class="w-100" /></div>
            <div class="position-absolute d-flex justify-content-between w-100 px-10" style="bottom: 0; z-index: 1;"><RectangleIcon class="w-100" /></div>
            <div class="d-flex justify-content-center align-items-center h-100 position-relative" style="z-index: 2;"><img :src="bgLogin" alt="Lupa Password" style="max-width: 100%; max-height: 100%;"></div>
        </div>
    </div>
</template>
