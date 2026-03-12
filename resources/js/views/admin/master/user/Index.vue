<template>
    <div>
        <div>
            <div id="main-content">
                <!--begin::Post-->
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <!--begin::Container-->
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5 align-items-center">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-dark mb-2">Master User</span>
                                        <span class="text-muted fs-6">Berikut Merupakan Data Master User</span>
                                    </h3>
                                    <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                        Tambah Data
                                    </button>

                                </div>
                                <div class="card-body pt-5">
                                    <DataTable :config="userStore.table" @get-data="userStore.getData"
                                        @set-order="(order: string) => userStore.setOrder(order)"
                                        @set-page="(page: number) => userStore.setCurrentPage(page)"
                                        @set-search="(search: string) => userStore.setSearch(search)"
                                        @set-show-per-page="(showPerPage: number) => userStore.setShowPerPage(showPerPage)"
                                        @set-sort-by="(sortBy: string) => userStore.setSortBy(sortBy)"
                                        :is-from-store="true">
                                        <template v-slot:body>
                                            <tr v-for="(context, index) in userStore.table.data">
                                                <td class="text-center">
                                                    {{ index + ((Number(userStore.table.showPerPage) *
                                                        (Number(userStore.table.currentPage) - 1))) + 1 }}
                                                </td>
                                                <td class="text-left">{{ context.name }}</td>
                                                <td class="text-left">{{ context.username }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-light-primary">{{ context?.role?.name
                                                    }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-center w-100 d-flex justify-content-center">
                                                        <div
                                                            class="form-check form-switch form-check-success form-check-solid justify-content-center">
                                                            <input class="form-check-input h-20px w-40px"
                                                                type="checkbox" value="1" :checked="context.isActive"
                                                                @click="changeStatus(context.id)">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="dropdown" style="position:static">
                                                        <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                            role="button" id="dropdownMenuLink"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            Opsi
                                                        </a>

                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="dropdownMenuLink">

                                                            <li>
                                                                <button class="dropdown-item"
                                                                    @click="edit(context?.id)">
                                                                    <span class="svg-icon svg-icon-primary ">
                                                                        <EditIcon />
                                                                    </span>
                                                                    Edit
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item"
                                                                    @click="confirmResetPassword(context?.id)">
                                                                    <span class="svg-icon svg-icon-danger ">
                                                                        <ResetPasswordIcon />
                                                                    </span>
                                                                    Reset Password
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </td>
                                            </tr>
                                        </template>
                                    </DataTable>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Container-->
                </div>

                <!--end::Post-->
            </div>

            <!-- end of content -->
        </div>
        <!--end::Wrapper-->

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah User' : 'Edit User'}`" :subtitle="`Silahkan lengkapi form berikut untuk
                        ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.name.$error }">
                        Nama Lengkap
                    </label>
                    <input class="form-control" type="text" autocomplete="off" placeholder="Contoh: Arifin Zaidi"
                        v-model="single.name">
                    <div v-if="v$.single.name.$error" class="text-danger">
                        Nama Lengkap tidak boleh kosong!
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.username.$error }">
                        Username
                    </label>
                    <input class="form-control" type="text" autocomplete="off" placeholder="Contoh: arif123"
                        v-model="single.username">
                    <div v-if="v$.single.username.$error" class="text-danger">
                        Username tidak boleh kosong!
                    </div>
                </div>

                <div class="mb-5" v-if="flag === 'insert'">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.password.$error }">
                        Password
                    </label>
                    <input class="form-control" type="password" autocomplete="off" placeholder="Masukkan Password"
                        v-model="single.password">
                    <div v-if="v$.single.password.$error" class="text-danger">
                        Password tidak boleh kosong!
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.role.$error }">
                        Role
                    </label>
                    <SelectSingle v-model="single.role" :placeholder="'Pilih Role'"
                        :options="selectListStore.selectListRole" @on-search="selectListStore.getSelectListRole"
                        :loading="selectListStore.selectListLoading" :show_search="true" :serverside="true"
                        :show_button_clear="false" />
                    <div v-if="v$.single.role.$error" class="text-danger">Role tidak boleh kosong!</div>
                </div>
            </ModalBody>
            <ModalFooter>
                <button type="button" class="btn btn-light text-gray-700" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" @click="saveData">
                    <span class="indicator-label text-white">Simpan</span>
                </button>
            </ModalFooter>
        </CustomModal>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useUserStore } from "@stores/user";
import { useVuelidate } from '@vuelidate/core';
import { required, requiredIf } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, DataTable, ModalBody, ModalFooter, SelectSingle } from '@/components/main';
import { EditIcon, ResetPasswordIcon } from '@/components/icons';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { IRole, IUserDetail } from '@/types/user';
import { toast } from 'vue3-toastify';
import { ISelectOption } from '@/types/global';
import { useSelectListStore } from '@/stores/selectList';
import { useEnvironmentStore } from '@/stores/environment';

const userStore = useUserStore();
const selectListStore = useSelectListStore();
const environmentStore = useEnvironmentStore();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    id: '' as string,
    name: '' as string,
    username: '' as string,
    password: '' as string,
    role: {} as ISelectOption | IRole
});

const rules = computed(() => ({
    single: {
        name: { required },
        username: { required },
        role: { required },
        password: {
            required: requiredIf(() => flag.value === 'insert')
        }
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    userStore.resetTable();
    userStore.getData();
});

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

async function saveData() {
    await v$.value.$validate();
    if (v$.value.$error) {
        return;
    }

    const payload = {
        ...single,
        roleId: single.role?.id ?? '',
        role: {},
    };

    loaderShow();
    try {
        const res =
            flag.value === 'insert' && !single.id
                ? await userStore.create(payload)
                : await userStore.update(single.id, payload);

        $('#modal-form').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: res?.data?.meta?.message
        })

        userStore.setCurrentPage(1);
        userStore.getData()
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}
async function edit(id: string | number) {
    try {
        loaderShow();

        const res = await userStore.show(id);
        const data = res.data.data as IUserDetail

        reset();

        flag.value = 'edit';

        single.id = data.id;
        single.name = data.name
        single.username = data.username
        single.role = {
            id: data.role?.id,
            text: data.role?.name
        }
        modalForm.value?.show();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}


async function changeStatus(id: string | number) {

    try {
        loaderShow()
        const res = await userStore.changeStatus(id);
        toast.success(res.data.meta.message);
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function resetPassword(id: string | number) {
    loaderShow();
    try {
        const response = await userStore.resetPassword(id);
        toast.success(response.data.meta.message);
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}


async function confirmResetPassword(id: string | number) {
    Swal.fire({
        title: 'Reset Password?',
        text: "Password user akan dikembalikan ke default password yaitu:" + environmentStore.data.defaultPassword,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            resetPassword(id);
        }
    });
}


function reset() {
    v$.value.$reset();
    flag.value = 'insert';
    single.id = '';
    single.name = '';
}

</script>
<style scoped></style>
