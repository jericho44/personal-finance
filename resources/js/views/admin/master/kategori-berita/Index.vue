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
                                        <span class="card-label fw-bolder text-dark mb-2">Master Kategori
                                            Berita (ini contoh saja)</span>
                                        <span class="text-muted fs-6">Berikut Merupakan Data Master Berita</span>
                                    </h3>
                                    <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                        Tambah Data
                                    </button>

                                </div>
                                <div class="card-body pt-5">
                                    <DataTable :config="newsCategory.table" @get-data="newsCategory.getData"
                                        @set-order="(order: string) => newsCategory.setOrder(order)"
                                        @set-page="(page: number) => newsCategory.setCurrentPage(page)"
                                        @set-search="(search: string) => newsCategory.setSearch(search)"
                                        @set-show-per-page="(showPerPage: number) => newsCategory.setShowPerPage(showPerPage)"
                                        @set-sort-by="(sortBy: string) => newsCategory.setSortBy(sortBy)"
                                        :is-from-store="true">
                                        <template v-slot:body>
                                            <tr v-for="(context, index) in newsCategory.table.data">
                                                <td class="text-center">
                                                    {{ index + ((Number(newsCategory.table.showPerPage) *
                                                        (Number(newsCategory.table.currentPage) - 1))) + 1 }}
                                                </td>
                                                <td class="text-left">{{ context.name }}</td>
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
                                                    <button @click="edit(context?.id)" class="btn btn-secondary btn-xs"
                                                        type="button"
                                                        style="padding:5px 10px !important;color: #3E97FF;"
                                                        aria-expanded="false">
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.9607 6.0805L6.38415 11.9762C6.21888 12.1509 5.98897 12.2499 5.74846 12.2499L3.20857 12.2499C2.72532 12.2499 2.33357 11.8582 2.33357 11.3749L2.33357 8.81264C2.33357 8.58536 2.422 8.367 2.58015 8.20378L8.21459 2.38837C8.55085 2.04131 9.1048 2.03255 9.45187 2.36882C9.45518 2.37202 9.45847 2.37526 9.46172 2.37852L11.9437 4.86051C12.2787 5.19552 12.2862 5.73631 11.9607 6.0805Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        &ensp; Edit
                                                    </button>
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

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah Kategori Berita' : 'Edit Kategori Berita'}`"
            :subtitle="`Silahkan lengkapi form berikut untuk
                        ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="">
            <ModalBody>
                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.name.$error ? 'text-danger' : ''">Nama
                        Kategori</label>
                    <input class="form-control" type="text" placeholder="Contoh: Olahraga" autocomplete="off"
                        v-model="single.name">
                    <div v-if="v$.single.name.$error" class="text-danger"> Nama Kategori tidak boleh kosong!
                    </div>
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
import { useNewsCategory } from "@stores/newsCategory";
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, DataTable, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { INewsCategoryDetail } from '@/types/news-category';
import { toast } from 'vue3-toastify';

const newsCategory = useNewsCategory();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    id: '' as string,
    name: ''
});

const rules = computed(() => ({
    single: {
        name: { required }
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    newsCategory.resetTable();
    newsCategory.getData()
});

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

async function saveData() {
    await v$.value.$validate();
    if (v$.value.$error) return;
    const payload = {
        name: single.name
    };
    loaderShow()
    try {
        const res =
            flag.value === 'insert' && !single.id
                ? await newsCategory.create(payload)
                : await newsCategory.update(single.id, payload);

        $('#modal-form').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: res?.data?.meta?.message
        })

        newsCategory.setCurrentPage(1);
        newsCategory.getData()
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function edit(id: string | number) {

    try {
        loaderShow()

        const res = await newsCategory.show(id);
        const data = res.data.data as INewsCategoryDetail

        reset();

        flag.value = 'edit';
        single.id = data.id;
        single.name = data.name
        modalForm.value?.show();
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function changeStatus(id: string | number) {

    try {
        loaderShow()
        const res = await newsCategory.changeStatus(id);
        toast.success(res.data.meta.message);
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

function reset() {
    v$.value.$reset();
    flag.value = 'insert';
    single.id = '';
    single.name = '';
}
</script>


<style scoped></style>
