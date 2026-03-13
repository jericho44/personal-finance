<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark mb-2">Tagihan & Langganan</span>
                                    <span class="text-muted fs-6">Daftar Tagihan dan Pembayaran Berulang</span>
                                </h3>
                                <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                    Tambah Tagihan
                                </button>
                            </div>
                            <div class="card-body pt-5">
                                <!-- Reminder Bills -->
                                <div v-if="upcomingBills.length > 0" class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-8">
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-bold">
                                            <h4 class="text-gray-900 fw-bolder">Pengingat Tagihan!</h4>
                                            <div class="fs-6 text-gray-700">
                                                Terdapat <strong class="text-danger">{{ upcomingBills.length }} tagihan</strong> yang akan jatuh tempo dalam beberapa hari ke depan dan belum dibayar.
                                            </div>
                                            <ul class="text-gray-700 mt-2 mb-0">
                                                <li v-for="b in upcomingBills" :key="b.idHash">
                                                    {{ b.name }} ({{ formatCurrency(b.amount) }}) - <span class="text-danger">Jatuh tempo: {{ formatDate(b.dueDate) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Reminder Bills -->
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mt-5">
                                        <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2 text-center">No</th>
                                                <th class="min-w-125px">Nama Tagihan</th>
                                                <th class="min-w-125px">Jatuh Tempo</th>
                                                <th class="min-w-100px">Frekuensi</th>
                                                <th class="min-w-100px">Nominal</th>
                                                <th class="min-w-100px">Status</th>
                                                <th class="text-center min-w-100px">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                            <tr v-if="bills.length === 0">
                                                <td colspan="7" class="text-center">Belum ada data tagihan.</td>
                                            </tr>
                                            <tr v-for="(bill, index) in bills" :key="bill.idHash">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ bill.name }}</span>
                                                        <span v-if="bill.category" class="text-muted fs-7">
                                                            Kategori: {{ bill.category.name }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ formatDate(bill.dueDate) }}</td>
                                                <td><span class="badge badge-light-primary">{{ mapFrequency(bill.frequency) }}</span></td>
                                                <td>{{ formatCurrency(bill.amount) }}</td>
                                                <td>
                                                    <span v-if="bill.isPaid" class="badge badge-light-success">Lunas</span>
                                                    <span v-else class="badge badge-light-danger">Belum Dibayar</span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(bill)">
                                                        <span class="svg-icon svg-icon-primary"><i class="fa fa-pen" /></span>
                                                    </button>
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(bill.idHash)">
                                                        <span class="svg-icon svg-icon-danger"><i class="fa fa-trash" /></span>
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

        <CustomModal ref="modalForm" :title="flag === 'insert' ? 'Tambah Tagihan' : 'Edit Tagihan'" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.name.$error }">Nama Tagihan</label>
                        <input class="form-control" type="text" placeholder="Misal: Tagihan Listrik" v-model="single.name">
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.amount.$error }">Nominal</label>
                        <input class="form-control" type="number" placeholder="0" v-model="single.amount" step="0.01">
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.due_date.$error }">Tanggal Jatuh Tempo</label>
                        <input class="form-control" type="date" v-model="single.due_date">
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.frequency.$error }">Frekuensi</label>
                        <select class="form-select" v-model="single.frequency">
                            <option value="once">Sekali</option>
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Kategori Rekomendasi (Opsional)</label>
                        <select class="form-select" v-model="single.category_id">
                            <option value="">Tanpa Kategori</option>
                            <option v-for="category in categories" :key="category.idHash" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Status Pembayaran</label>
                        <select class="form-select" v-model="single.is_paid">
                            <option value="false">Belum Dibayar</option>
                            <option value="true">Lunas</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-5">
                        <label class="form-label fw-bolder fs-6">Catatan (Opsional)</label>
                        <textarea class="form-control" rows="3" placeholder="Tulis catatan..." v-model="single.notes"></textarea>
                    </div>
                </div>
            </ModalBody>
            <ModalFooter>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" @click="saveData">Simpan</button>
            </ModalFooter>
        </CustomModal>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useBillStore } from "@stores/bill";
import { useCategoryStore } from "@stores/category";
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, minValue } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, loaderHide, loaderShow } from '@/plugins/global';
import { toast } from 'vue3-toastify';
import dayjs from 'dayjs';

const billStore = useBillStore();
const categoryStore = useCategoryStore();
const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);
const bills = ref<any[]>([]);
const categories = ref<any[]>([]);
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    idHash: '',
    name: '',
    amount: '',
    due_date: dayjs().format('YYYY-MM-DD'),
    frequency: 'monthly',
    is_paid: 'false',
    category_id: '',
    notes: '',
});

const upcomingBills = computed(() => {
    const today = dayjs().startOf('day');
    const nextWeek = today.add(7, 'day');
    return bills.value.filter(b => {
        if (b.isPaid) return false;
        const due = dayjs(b.dueDate).startOf('day');
        return due.isAfter(today.subtract(1, 'day')) && due.isBefore(nextWeek.add(1, 'day'));
    });
});

const rules = computed(() => ({
    single: {
        name: { required },
        amount: { required, numeric, minValue: minValue(0.01) },
        due_date: { required },
        frequency: { required }
    }
}));
const v$ = useVuelidate(rules, { single });

onMounted(async () => {
    loaderShow();
    await fetchCategories();
    await fetchBills();
    loaderHide();
});

async function fetchCategories() {
    try {
        const res = await categoryStore.getAll(1, 0);
        categories.value = res.data.data;
    } catch (e) { }
}

async function fetchBills() {
    try {
        loaderShow();
        const res = await billStore.getAll();
        bills.value = res.data.data;
    } catch (error) {
        axiosHandleError(error);
    } finally { loaderHide(); }
}

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

function edit(item: any) {
    reset();
    flag.value = 'edit';
    single.idHash = item.idHash;
    single.name = item.name;
    single.amount = item.amount;
    single.due_date = dayjs(item.dueDate).format('YYYY-MM-DD');
    single.frequency = item.frequency;
    single.is_paid = item.isPaid ? 'true' : 'false';
    single.category_id = item.categoryId || '';
    single.notes = item.notes || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: any = {
        name: single.name,
        amount: Number(single.amount),
        due_date: single.due_date,
        frequency: single.frequency,
        is_paid: single.is_paid === 'true' ? 1 : 0,
        category_id: single.category_id ? Number(single.category_id) : null,
        notes: single.notes
    };

    try {
        loaderShow();
        if (flag.value === 'insert') {
            await billStore.create(payload);
        } else {
            await billStore.update(single.idHash, payload);
        }
        modalForm.value?.hide();
        toast.success('Tagihan berhasil disimpan.');
        fetchBills();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(idHash: string) {
    Swal.fire({
        title: 'Hapus Tagihan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await billStore.destroy(idHash);
                toast.success('Tagihan berhasil dihapus');
                fetchBills();
            } catch (error) {
                axiosHandleError(error);
            } finally { loaderHide(); }
        }
    });
}

function reset() {
    v$.value.$reset();
    flag.value = 'insert';
    single.idHash = '';
    single.name = '';
    single.amount = '';
    single.due_date = dayjs().format('YYYY-MM-DD');
    single.frequency = 'monthly';
    single.is_paid = 'false';
    single.category_id = '';
    single.notes = '';
}

function mapFrequency(freq: string) {
    const map: any = { once: 'Sekali', daily: 'Harian', weekly: 'Mingguan', monthly: 'Bulanan', yearly: 'Tahunan' };
    return map[freq] || freq;
}

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}

function formatDate(date: string) {
    return dayjs(date).format('DD MMM YYYY');
}
</script>
