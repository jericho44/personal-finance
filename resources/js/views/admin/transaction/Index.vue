<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-gray-900 mb-2">Riwayat Transaksi</span>
                                    <span class="text-muted fs-6">Daftar Pemasukan, Pengeluaran, dan Transfer</span>
                                </h3>
                                <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                    Tambah Transaksi
                                </button>
                            </div>
                            <div class="card-body pt-5">
                                <!-- Filter Section -->
                                <div class="row mb-5">
                                    <div class="col-md-2 mb-3">
                                        <select class="form-select" v-model="filters.type" @change="fetchTransactions">
                                            <option value="all">Semua Tipe</option>
                                            <option value="income">Pemasukan</option>
                                            <option value="expense">Pengeluaran</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select class="form-select" v-model="filters.account_id" @change="fetchTransactions">
                                            <option value="">Semua Akun</option>
                                            <option v-for="account in accounts" :key="account.idHash" :value="account.id">
                                                {{ account.name }} ({{ account.currency }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select class="form-select" v-model="filters.category_id" @change="fetchTransactions">
                                            <option value="">Semua Kategori</option>
                                            <option v-for="category in categories" :key="category.idHash" :value="category.id">
                                                {{ category.name }} ({{ category.type }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <input type="date" class="form-control" v-model="filters.start_date" @change="fetchTransactions" placeholder="Start Date">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <input type="date" class="form-control" v-model="filters.end_date" @change="fetchTransactions" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mt-5">
                                        <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2 text-center">No</th>
                                                <th class="min-w-125px">Tanggal</th>
                                                <th class="min-w-125px">Akun</th>
                                                <th class="min-w-125px">Kategori</th>
                                                <th class="min-w-100px">Nominal</th>
                                                <th class="min-w-125px">Catatan</th>
                                                <th class="text-center min-w-100px">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                            <tr v-if="transactions.length === 0">
                                                <td colspan="7" class="text-center">Belum ada data transaksi.</td>
                                            </tr>
                                            <tr v-for="(transaction, index) in transactions" :key="transaction.idHash">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>{{ formatDate(transaction.date) }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ transaction.account?.name }}</span>
                                                        <span v-if="transaction.type === 'transfer'" class="text-muted fs-7">
                                                            <i class="fa fa-arrow-right mx-1"></i> {{ transaction.targetAccount?.name }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span v-if="transaction.category" class="badge badge-light">
                                                        <i v-if="transaction.category.icon" :class="transaction.category.icon + ' me-1'"></i>
                                                        {{ transaction.category.name }}
                                                    </span>
                                                    <span v-else class="text-muted">-</span>
                                                </td>
                                                <td>
                                                    <span :class="{'text-success fw-bolder': transaction.type === 'income', 'text-danger fw-bolder': transaction.type === 'expense', 'text-primary fw-bolder': transaction.type === 'transfer'}">
                                                        {{ transaction.type === 'income' ? '+' : (transaction.type === 'expense' ? '-' : '') }} {{ formatCurrency(transaction.amount, transaction.account?.currency || 'IDR') }}
                                                    </span>
                                                </td>
                                                <td>{{ transaction.notes || '-' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(transaction)">
                                                        <span class="svg-icon svg-icon-primary">
                                                            <i class="fa fa-pen" />
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(transaction.id)">
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

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah Transaksi' : 'Edit Transaksi'}`" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.type.$error }">
                            Tipe Transaksi
                        </label>
                        <select class="form-select" v-model="single.type" @change="onTypeChange">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                            <option value="transfer">Transfer Antar Akun</option>
                        </select>
                        <div v-if="v$.single.type.$error" class="text-danger">
                            Tipe Transaksi tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.amount.$error }">
                            Nominal
                        </label>
                        <input class="form-control" type="number" autocomplete="off" placeholder="0" v-model="single.amount" step="0.01">
                        <div v-if="v$.single.amount.$error" class="text-danger">
                            Nominal harus berupa angka lebih dari 0!
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.date.$error }">
                            Tanggal Transaksi
                        </label>
                        <input class="form-control" type="date" v-model="single.date">
                        <div v-if="v$.single.date.$error" class="text-danger">
                            Tanggal tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.account_id.$error }">
                            Akun {{ single.type === 'transfer' ? 'Asal' : '' }}
                        </label>
                        <select class="form-select" v-model="single.account_id">
                            <option value="">Pilih Akun...</option>
                            <option v-for="account in accounts" :key="account.idHash" :value="account.id">
                                {{ account.name }} ({{ account.currency }}) - Saldo: {{ account.balance }}
                            </option>
                        </select>
                        <div v-if="v$.single.account_id.$error" class="text-danger">
                            Akun tidak boleh kosong!
                        </div>
                    </div>

                    <div v-if="single.type === 'transfer'" class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.target_account_id.$error }">
                            Akun Tujuan
                        </label>
                        <select class="form-select" v-model="single.target_account_id">
                            <option value="">Pilih Akun Tujuan...</option>
                            <option v-for="account in filteredTargetAccounts" :key="account.idHash" :value="account.id">
                                {{ account.name }} ({{ account.currency }})
                            </option>
                        </select>
                        <div v-if="v$.single.target_account_id.$error" class="text-danger">
                            Akun Tujuan tidak boleh kosong untuk transaksi Transfer!
                        </div>
                    </div>

                    <div v-if="single.type !== 'transfer'" class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">
                            Kategori (Opsional)
                        </label>
                        <select class="form-select" v-model="single.category_id">
                            <option value="">Tanpa Kategori</option>
                            <option v-for="category in filteredCategories" :key="category.idHash" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-5">
                        <label class="form-label fw-bolder fs-6">
                            Catatan (Opsional)
                        </label>
                        <textarea class="form-control" rows="3" placeholder="Tulis catatan transaksi..." v-model="single.notes"></textarea>
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
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useTransactionStore } from "@stores/transaction";
import { useAccountStore } from "@stores/account";
import { useCategoryStore } from "@stores/category";
import { useVuelidate } from '@vuelidate/core';
import { required, requiredIf, numeric, minValue } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { ITransaction, ITransactionPayload, ITransactionFilters } from '@/types/transaction';
import { IAccount } from '@/types/account';
import { ICategory } from '@/types/category';
import { toast } from 'vue3-toastify';
import dayjs from 'dayjs';

const transactionStore = useTransactionStore();
const accountStore = useAccountStore();
const categoryStore = useCategoryStore();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const transactions = ref<ITransaction[]>([]);
const accounts = ref<IAccount[]>([]);
const categories = ref<ICategory[]>([]);

const flag = ref<'insert' | 'edit'>('insert');

const filters = reactive<ITransactionFilters>({
    type: 'all',
    account_id: '',
    category_id: '',
    start_date: dayjs().startOf('month').format('YYYY-MM-DD'),
    end_date: dayjs().endOf('month').format('YYYY-MM-DD'),
});

const single = reactive({
    idHash: '' as string,
    account_id: '' as number | string,
    target_account_id: '' as number | string,
    category_id: '' as number | string,
    type: 'expense' as 'income' | 'expense' | 'transfer',
    amount: '' as number | string,
    date: dayjs().format('YYYY-MM-DD') as string,
    notes: '' as string,
});

const rules = computed(() => ({
    single: {
        type: { required },
        amount: { required, numeric, minValue: minValue(0.01) },
        date: { required },
        account_id: { required },
        target_account_id: { requiredIfFunction: requiredIf(() => single.type === 'transfer') }
    }
}));

const v$ = useVuelidate(rules, { single });

const filteredCategories = computed(() => {
    return categories.value.filter(c => c.type === single.type);
});

const filteredTargetAccounts = computed(() => {
    return accounts.value.filter(a => a.id !== single.account_id);
});

onMounted(async () => {
    initializeAppPlugins();
    loaderShow();
    await fetchHelpers();
    await fetchTransactions();
    loaderHide();
});

async function fetchHelpers() {
    try {
        const [accRes, catRes] = await Promise.all([
            accountStore.getAll(),
            categoryStore.getAll(1, 0)
        ]);
        accounts.value = accRes.data.data;
        categories.value = catRes.data.data;
    } catch (error) {
        axiosHandleError(error);
    }
}

async function fetchTransactions() {
    try {
        loaderShow();
        const payloadFilters = {
            ...filters,
            account_id: filters.account_id || null,
            category_id: filters.category_id || null,
        };
        const res = await transactionStore.getAll(payloadFilters);
        transactions.value = res.data.data;
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

function onTypeChange() {
    single.category_id = '';
    if (single.type !== 'transfer') {
        single.target_account_id = '';
    }
}

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

function edit(item: ITransaction) {
    reset();
    flag.value = 'edit';
    single.idHash = item.idHash;
    single.account_id = item.accountId;
    single.target_account_id = item.targetAccountId || '';
    single.category_id = item.categoryId || '';
    single.type = item.type;
    single.amount = item.amount;
    single.date = dayjs(item.date).format('YYYY-MM-DD');
    single.notes = item.notes || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: ITransactionPayload = {
        account_id: Number(single.account_id),
        target_account_id: single.target_account_id ? Number(single.target_account_id) : null,
        category_id: single.category_id ? Number(single.category_id) : null,
        type: single.type,
        amount: Number(single.amount),
        date: single.date,
        notes: single.notes
    };

    try {
        loaderShow();
        if (flag.value === 'insert') {
            await transactionStore.create(payload);
        } else {
            await transactionStore.update(single.idHash, payload);
        }

        modalForm.value?.hide();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data transaksi berhasil disimpan.'
        });

        fetchTransactions();
        fetchHelpers(); // Update account balances in background
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(idHash: string) {
    Swal.fire({
        title: 'Hapus Transaksi?',
        text: "Transaksi ini akan dihapus permanen. Saldo akun mungkin akan disesuaikan kembali.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await transactionStore.destroy(idHash);
                toast.success('Transaksi berhasil dihapus');
                fetchTransactions();
                fetchHelpers(); // Update balances
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
    single.idHash = '';
    single.account_id = '';
    single.target_account_id = '';
    single.category_id = '';
    single.type = 'expense';
    single.amount = '';
    single.date = dayjs().format('YYYY-MM-DD');
    single.notes = '';
}

function formatCurrency(amount: number, currency: string) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: currency }).format(amount);
}

function formatDate(date: string) {
    return dayjs(date).format('DD MMM YYYY');
}

</script>
