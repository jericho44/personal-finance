<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark mb-2">Kategori Keuangan</span>
                                    <span class="text-muted fs-6">Kelola Kategori Pemasukan dan Pengeluaran</span>
                                </h3>
                                <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                    Tambah Kategori
                                </button>
                            </div>
                            <div class="card-body pt-5">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mt-5">
                                        <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2 text-center">No</th>
                                                <th class="min-w-125px">Nama</th>
                                                <th class="min-w-125px">Tipe</th>
                                                <th class="min-w-125px">Warna / Ikon</th>
                                                <th class="text-center min-w-100px">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                            <tr v-if="categories.length === 0">
                                                <td colspan="5" class="text-center">Belum ada data kategori.</td>
                                            </tr>
                                            <tr v-for="(category, index) in categories" :key="category.id_hash">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>{{ category.name }}</td>
                                                <td>
                                                    <span class="badge" :class="category.type === 'income' ? 'badge-light-success' : 'badge-light-danger'">
                                                        {{ category.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-circle w-20px h-20px me-2" :style="{ backgroundColor: category.color || '#cccccc' }"></span>
                                                        <i v-if="category.icon" :class="category.icon" class="fs-4"></i>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(category)">
                                                        <span class="svg-icon svg-icon-primary">
                                                            <i class="fa fa-pen" />
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(category.id_hash)">
                                                        <span class="svg-icon svg-icon-danger">
                                                            <i class="fa fa-trash" />
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah Kategori' : 'Edit Kategori'}`" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-md">
            <ModalBody>
                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.name.$error }">
                        Nama Kategori
                    </label>
                    <input class="form-control" type="text" autocomplete="off" placeholder="Contoh: Gaji, Makanan" v-model="single.name">
                    <div v-if="v$.single.name.$error" class="text-danger">
                        Nama Kategori tidak boleh kosong!
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.type.$error }">
                        Tipe Kategori
                    </label>
                    <select class="form-select" v-model="single.type">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    <div v-if="v$.single.type.$error" class="text-danger">
                        Tipe Kategori tidak boleh kosong!
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label mb-3 fw-bolder fs-6">
                        Warna Kategori (Opsional)
                    </label>
                    <input type="color" class="form-control form-control-color w-100" v-model="single.color" title="Pilih warna kategori">
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6">
                        Ikon Kategori (Opsional)
                    </label>
                    <input class="form-control" type="text" autocomplete="off" placeholder="Class ikon, misal: fa fa-utensils" v-model="single.icon">
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
import { useCategoryStore } from "@stores/category";
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { ICategory, ICategoryPayload } from '@/types/category';
import { toast } from 'vue3-toastify';

const categoryStore = useCategoryStore();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const categories = ref<ICategory[]>([]);
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    id_hash: '' as string,
    name: '' as string,
    type: 'expense' as 'income' | 'expense',
    color: '#3498db' as string,
    icon: '' as string,
});

const rules = computed(() => ({
    single: {
        name: { required },
        type: { required },
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    fetchCategories();
});

async function fetchCategories() {
    try {
        loaderShow();
        const res = await categoryStore.getAll();
        categories.value = res.data.data;
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

function edit(category: ICategory) {
    reset();
    flag.value = 'edit';
    single.id_hash = category.id_hash;
    single.name = category.name;
    single.type = category.type;
    single.color = category.color || '#3498db';
    single.icon = category.icon || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: ICategoryPayload = {
        name: single.name,
        type: single.type,
        color: single.color,
        icon: single.icon
    };

    try {
        loaderShow();
        let res;
        if (flag.value === 'insert') {
            res = await categoryStore.create(payload);
        } else {
            res = await categoryStore.update(single.id_hash, payload);
        }

        modalForm.value?.hide();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data kategori berhasil disimpan.'
        });

        fetchCategories();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(id_hash: string) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Kategori ini akan dihapus secara permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await categoryStore.destroy(id_hash);
                toast.success('Kategori berhasil dihapus');
                fetchCategories();
            } catch (error) {
                axiosHandleError(error);
            } finally {
                loaderHide();
            }
        }
    });
}

function reset() {
    v$.value.$reset();
    flag.value = 'insert';
    single.id_hash = '';
    single.name = '';
    single.type = 'expense';
    single.color = '#3498db';
    single.icon = '';
}

</script>
