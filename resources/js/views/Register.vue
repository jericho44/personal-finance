<script setup lang="ts">
import { useAuthorizationStore } from "@stores/authorization";
import { reactive, ref, onMounted } from "vue";
import useVuelidate from "@vuelidate/core";
import { minLength, required, sameAs } from "@vuelidate/validators";

import { ErrorFormValidation } from "@/components/main";
import { AxiosResponse } from "axios";
import { useRouter } from "vue-router";
import { initializeAppPlugins, loaderShow, loaderHide, getAssetUrl, axiosHandleError } from '@plugins/global';
import { RectangleIcon } from "@/components/icons";

const router = useRouter();
const authorizationStore = useAuthorizationStore();

const bgLogin = ref(getAssetUrl() + 'extends/images/bg-login.png');

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const disabledButton = ref(false);

const single = reactive({
    name: "",
    username: "",
    password: "",
    password_confirmation: "",
});

const rules = {
    name: { required },
    username: { required, minLength: minLength(4) },
    password: { required, minLength: minLength(6) },
    password_confirmation: { required, sameAsPassword: sameAs(single.password) },
};

const v$ = useVuelidate(rules, single);

onMounted(() => {
    initializeAppPlugins();
});

async function register() {
    try {
        v$.value.$touch();
        if (v$.value.$invalid) return;

        if (disabledButton.value) return false;

        disabledButton.value = true;
        loaderShow();

        const payload = {
            name: single.name,
            username: single.username,
            password: single.password,
            password_confirmation: single.password_confirmation,
        };

        const response = await authorizationStore.register(payload) as AxiosResponse;
        if (response && response.status === 200) {
            router.push({ name: 'login' });
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
            <div class="container d-flex align-items-center justify-content-center pt-10" style="height:100%; overflow-y: auto;">
                <div class="d-flex align-items-center justify-content-center w-100 h-100 my-5" style="position: relative; z-index: 3;">
                    <div class="card w-100" style="max-width: 500px; border-radius: 14px; background-color: #f4f4f4; border: 1px solid #D9DBE4;">
                        <div class="card-body d-flex flex-column gap-5" style="font-size: 1.5rem;">
                            <div class="text-center mt-5">
                                <h3 class="mb-2" style="font-size: 2.5rem;">Buat Akun</h3>
                                <div style="color: #7E8299; font-size: 1.35rem;">Silakan isi data berikut</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" :class="{ 'text-danger': v$.name.$error }" style="color: #3F4254; font-size: 1.35rem;">Nama Lengkap</label>
                                <div class="input-group">
                                    <input type="text" v-model="single.name" class="form-control" style="font-size: 1.35rem; height: 55px;" placeholder="Masukan nama lengkap">
                                </div>
                                <ErrorFormValidation :vfield="v$.name" />
                            </div>

                            <div class="form-group">
                                <label class="form-label" :class="{ 'text-danger': v$.username.$error }" style="color: #3F4254; font-size: 1.35rem;">Username</label>
                                <div class="input-group">
                                    <input type="text" v-model="single.username" class="form-control" style="font-size: 1.35rem; height: 55px;" placeholder="Masukan username">
                                </div>
                                <ErrorFormValidation :vfield="v$.username" />
                            </div>

                            <div class="form-group">
                                <label class="form-label" :class="{ 'text-danger': v$.password.$error }" style="color: #3F4254; font-size: 1.35rem;">Password</label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" v-model="single.password" class="form-control" style="font-size: 1.35rem; height: 55px; border-right: 0 !important;" placeholder="Masukan password">
                                    <div style="height: 55px; border-left: 0 !important; background: #fff !important" class="input-group-text cursor-pointer" @click="showPassword = !showPassword">
                                        <i v-if="!showPassword" class="fa fa-eye" style="font-size: 1.35rem;" />
                                        <i v-if="showPassword" class="fa fa-eye-slash" style="font-size: 1.35rem;" />
                                    </div>
                                </div>
                                <ErrorFormValidation :vfield="v$.password" />
                            </div>

                            <div class="form-group">
                                <label class="form-label" :class="{ 'text-danger': v$.password_confirmation.$error }" style="color: #3F4254; font-size: 1.35rem;">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input :type="showPasswordConfirmation ? 'text' : 'password'" v-model="single.password_confirmation" class="form-control" style="font-size: 1.35rem; height: 55px; border-right: 0 !important;" placeholder="Masukan kembali password">
                                    <div style="height: 55px; border-left: 0 !important; background: #fff !important" class="input-group-text cursor-pointer" @click="showPasswordConfirmation = !showPasswordConfirmation">
                                        <i v-if="!showPasswordConfirmation" class="fa fa-eye" style="font-size: 1.35rem;" />
                                        <i v-if="showPasswordConfirmation" class="fa fa-eye-slash" style="font-size: 1.35rem;" />
                                    </div>
                                </div>
                                <ErrorFormValidation :vfield="v$.password_confirmation" />
                            </div>

                            <button @click="register" class="btn btn-primary w-100" style="font-size: 1.5rem; height: 55px;">Daftar</button>

                            <div class="text-center mt-3" style="font-size: 1.35rem; color: #7E8299;">
                                Sudah punya akun? 
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
            <div class="d-flex justify-content-center align-items-center h-100 position-relative" style="z-index: 2;"><img :src="bgLogin" alt="Register" style="max-width: 100%; max-height: 100%;"></div>
        </div>
    </div>
</template>
