<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark mb-2">Manajemen Anggaran</span>
                                    <span class="text-muted fs-6">Pantau dan kelola batas pengeluaran Anda</span>
                                </h3>
                                <div class="d-flex align-items-center">
                                    <select class="form-select w-200px me-3" v-model="selectedPeriod" @change="fetchProgress">
                                        <option value="current_month">Bulan Ini</option>
                                        <option value="last_month">Bulan Lalu</option>
                                    </select>
                                    <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                        Buat Anggaran
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-5">
                                <div class="row" v-if="budgetProgress.length > 0">
                                    <div class="col-md-6 mb-7" v-for="item in budgetProgress" :key="item.budget.id_hash">
                                        <div class="border border-dashed border-gray-300 rounded p-5">
                                            <div class="d-flex align-items-center justify-content-between mb-4">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-circle w-30px h-30px me-3" :style="{ backgroundColor: item.budget.category?.color || '#cccccc' }">
                                                        <i v-if="item.budget.category?.icon" :class="item.budget.category.icon + ' text-white fs-6'"></i>
                                                    </span>
                                                    <span class="text-dark fw-bolder fs-5">{{ item.budget.category?.name || 'Kategori Dihapus' }}</span>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(item.budget)">
                                                        <span class="svg-icon svg-icon-primary">
                                                            <i class="fa fa-pen" />
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(item.budget.id_hash)">
                                                        <span class="svg-icon svg-icon-danger">
                                                            <i class="fa fa-trash" />
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted fw-bold">Terpakai: {{ formatCurrency(item.spent) }}</span>
                                                <span class="text-dark fw-bolder">Batas: {{ formatCurrency(item.budget.amount) }}</span>
                                            </div>

                                            <div class="h-8px w-100 bg-light rounded mb-2">
                                                <div class="rounded h-100" 
                                                    :class="{
                                                        'bg-success': item.percentage <= 50,
                                                        'bg-warning': item.percentage > 50 && item.percentage <= 80,
                                                        'bg-danger': item.percentage > 80
                                                    }" 
                                                    role="progressbar" 
                                                    :style="{ width: `${item.percentage}%` }">
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted fs-7">{{ item.percentage }}%</span>
                                                <span class="fs-7 fw-bolder" :class="item.is_over_budget ? 'text-danger' : 'text-success'">
                                                    {{ item.is_over_budget ? 'Melebihi anggaran' : `Tersisa: ${formatCurrency(item.remaining)}` }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-10">
                                    <svg class="mb-5" xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 24 24" fill="none">
                                        <rect x="4" y="2" width="16" height="20" rx="2" stroke="#c4c4c4" stroke-width="1.5" fill="#f5f8fa"/>
                                        <rect x="4" y="2" width="16" height="5" rx="2" fill="#e1e3ea"/>
                                        <line x1="8" y1="10" x2="16" y2="10" stroke="#c4c4c4" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="8" y1="13.5" x2="14" y2="13.5" stroke="#c4c4c4" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="8" y1="17" x2="12" y2="17" stroke="#c4c4c4" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <h4 class="text-muted fw-bold">Belum ada anggaran untuk periode ini.</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Buat Anggaran' : 'Edit Anggaran'}`" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.category_id.$error }">
                            Kategori (Pengeluaran)
                        </label>
                        <select class="form-select" v-model="single.category_id">
                            <option value="">Pilih Kategori...</option>
                            <option v-for="category in categories" :key="category.id_hash" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <div v-if="v$.single.category_id.$error" class="text-danger">
                            Kategori tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.amount.$error }">
                            Batas Anggaran
                        </label>
                        <input class="form-control" type="number" autocomplete="off" placeholder="0" v-model="single.amount" step="0.01">
                        <div v-if="v$.single.amount.$error" class="text-danger">
                            Batas Anggaran harus berupa angka lebih dari 0!
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.start_date.$error }">
                            Tanggal Mulai
                        </label>
                        <input class="form-control" type="date" v-model="single.start_date">
                        <div v-if="v$.single.start_date.$error" class="text-danger">
                            Tanggal Mulai tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.end_date.$error }">
                            Tanggal Selesai
                        </label>
                        <input class="form-control" type="date" v-model="single.end_date">
                        <div v-if="v$.single.end_date.$error" class="text-danger">
                            Tanggal Selesai tidak boleh kosong dan harus setelah tanggal mulai!
                        </div>
                    </div>

                    <div class="col-md-12 mb-5">
                        <label class="form-label fw-bolder fs-6">
                            Status
                        </label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" v-model="single.is_active" id="isActiveSwitch"/>
                            <label class="form-check-label" for="isActiveSwitch">
                                {{ single.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-5">
                        <label class="form-label fw-bolder fs-6">
                            Catatan (Opsional)
                        </label>
                        <textarea class="form-control" rows="3" placeholder="Tulis catatan..." v-model="single.notes"></textarea>
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
import { useBudgetStore } from "@stores/budget";
import { useCategoryStore } from "@stores/category";
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, minValue } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { IBudget, IBudgetPayload, IBudgetProgress } from '@/types/budget';
import { ICategory } from '@/types/category';
import { toast } from 'vue3-toastify';
import dayjs from 'dayjs';

const budgetStore = useBudgetStore();
const categoryStore = useCategoryStore();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const budgetProgress = ref<IBudgetProgress[]>([]);
const categories = ref<ICategory[]>([]);

const selectedPeriod = ref<'current_month' | 'last_month'>('current_month');
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    id_hash: '' as string,
    category_id: '' as number | string,
    amount: '' as number | string,
    start_date: dayjs().startOf('month').format('YYYY-MM-DD') as string,
    end_date: dayjs().endOf('month').format('YYYY-MM-DD') as string,
    is_active: true as boolean,
    notes: '' as string,
});

const rules = computed(() => ({
    single: {
        category_id: { required },
        amount: { required, numeric, minValue: minValue(0.01) },
        start_date: { required },
        end_date: { 
            required, 
            isAfterOrEqual: (val: string) => dayjs(val).isSame(dayjs(single.start_date)) || dayjs(val).isAfter(dayjs(single.start_date)) 
        }
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(async () => {
    initializeAppPlugins();
    loaderShow();
    await fetchCategories();
    await fetchProgress();
    loaderHide();
});

async function fetchCategories() {
    try {
        const res = await categoryStore.getAll();
        // Only get expense categories for budgeting
        categories.value = res.data.data.filter((c: ICategory) => c.type === 'expense');
    } catch (error) {
        axiosHandleError(error);
    }
}

async function fetchProgress() {
    try {
        loaderShow();
        const res = await budgetStore.getProgress(selectedPeriod.value);
        budgetProgress.value = res.data.data;
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

function edit(item: IBudget) {
    reset();
    flag.value = 'edit';
    single.id_hash = item.id_hash;
    single.category_id = item.category_id;
    single.amount = item.amount;
    single.start_date = dayjs(item.start_date).format('YYYY-MM-DD');
    single.end_date = dayjs(item.end_date).format('YYYY-MM-DD');
    single.is_active = item.is_active;
    single.notes = item.notes || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: IBudgetPayload = {
        category_id: Number(single.category_id),
        amount: Number(single.amount),
        start_date: single.start_date,
        end_date: single.end_date,
        is_active: single.is_active,
        notes: single.notes
    };

    try {
        loaderShow();
        if (flag.value === 'insert') {
            await budgetStore.create(payload);
        } else {
            await budgetStore.update(single.id_hash, payload);
        }

        modalForm.value?.hide();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data anggaran berhasil disimpan.'
        });

        fetchProgress();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(id_hash: string) {
    Swal.fire({
        title: 'Hapus Anggaran?',
        text: "Data anggaran akan dihapus secara permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await budgetStore.destroy(id_hash);
                toast.success('Anggaran berhasil dihapus');
                fetchProgress();
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
    single.category_id = '';
    single.amount = '';
    single.start_date = dayjs().startOf('month').format('YYYY-MM-DD');
    single.end_date = dayjs().endOf('month').format('YYYY-MM-DD');
    single.is_active = true;
    single.notes = '';
}

function formatCurrency(amount: number, currency: string = 'IDR') {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: currency }).format(amount);
}

</script>
