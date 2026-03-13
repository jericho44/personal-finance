<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-gray-900 mb-2">Manajemen Akun</span>
                                    <span class="text-muted fs-6">Kelola Akun Keuangan Anda</span>
                                </h3>
                                <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                    Tambah Akun
                                </button>
                            </div>
                            <div class="card-body pt-5">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mt-5">
                                        <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2 text-center">No</th>
                                                <th class="min-w-125px">Nama Akun</th>
                                                <th class="min-w-125px">Tipe</th>
                                                <th class="min-w-125px">Saldo</th>
                                                <th class="min-w-100px">Warna / Ikon</th>
                                                <th class="text-center min-w-100px">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                            <tr v-if="accounts.length === 0">
                                                <td colspan="6" class="text-center">Belum ada data akun.</td>
                                            </tr>
                                            <tr v-for="(account, index) in accounts" :key="account.idHash">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span class="text-gray-900 fw-bolder text-hover-primary mb-1 fs-6">{{ account.name }}</span>
                                                            <span class="text-muted fw-bold text-muted d-block fs-7">{{ account.currency }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-primary text-capitalize">
                                                        {{ account.type.replace('_', ' ') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span :class="account.balance < 0 ? 'text-danger fw-bolder' : 'text-success fw-bolder'">
                                                        {{ formatCurrency(account.balance, account.currency) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-circle w-20px h-20px me-2" :style="{ backgroundColor: account.color || '#cccccc' }"></span>
                                                        <i v-if="account.icon" :class="account.icon" class="fs-4"></i>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(account)">
                                                        <span class="svg-icon svg-icon-primary">
                                                            <i class="fa fa-pen" />
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(account.id)">
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

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah Akun' : 'Edit Akun'}`" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.name.$error }">
                            Nama Akun
                        </label>
                        <input class="form-control" type="text" autocomplete="off" placeholder="Contoh: Dompet, Rekening BCA" v-model="single.name">
                        <div v-if="v$.single.name.$error" class="text-danger">
                            Nama Akun tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.type.$error }">
                            Tipe Akun
                        </label>
                        <select class="form-select" v-model="single.type">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="bank">Rekening Bank</option>
                            <option value="ewallet">E-Wallet (OVO, Gopay, dll)</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="investment">Investasi</option>
                        </select>
                        <div v-if="v$.single.type.$error" class="text-danger">
                            Tipe Akun tidak boleh kosong!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.balance.$error }">
                            Saldo Awal
                        </label>
                        <input class="form-control" type="number" autocomplete="off" placeholder="0" v-model="single.balance" step="0.01">
                        <div v-if="v$.single.balance.$error" class="text-danger">
                            Saldo Awal harus berupa angka!
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.currency.$error }">
                            Mata Uang
                        </label>
                        <input class="form-control" type="text" autocomplete="off" placeholder="IDR, USD" v-model="single.currency">
                        <div v-if="v$.single.currency.$error" class="text-danger">
                            Mata Uang tidak boleh kosong! (Maks 3 huruf)
                        </div>
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label mb-3 fw-bolder fs-6">
                            Warna Label (Opsional)
                        </label>
                        <input type="color" class="form-control form-control-color w-100" v-model="single.color" title="Pilih warna akun">
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">
                            Ikon (Opsional)
                        </label>
                        <input class="form-control" type="text" autocomplete="off" placeholder="Class ikon, misal: fa fa-wallet" v-model="single.icon">
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
import { useAccountStore } from "@stores/account";
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, maxLength } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import { IAccount, IAccountPayload } from '@/types/account';
import { toast } from 'vue3-toastify';

const accountStore = useAccountStore();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);

const accounts = ref<IAccount[]>([]);
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    idHash: '' as string,
    name: '' as string,
    type: 'cash' as 'cash' | 'bank' | 'ewallet' | 'credit_card' | 'investment',
    balance: 0 as number,
    currency: 'IDR' as string,
    color: '#1abc9c' as string,
    icon: '' as string,
});

const rules = computed(() => ({
    single: {
        name: { required },
        type: { required },
        balance: { required, numeric },
        currency: { required, maxLength: maxLength(3) }
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    fetchAccounts();
});

async function fetchAccounts() {
    try {
        loaderShow();
        const res = await accountStore.getAll();
        accounts.value = res.data.data;
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

function edit(account: IAccount) {
    reset();
    flag.value = 'edit';
    single.idHash = account.idHash;
    single.name = account.name;
    single.type = account.type;
    single.balance = account.balance;
    single.currency = account.currency;
    single.color = account.color || '#1abc9c';
    single.icon = account.icon || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: IAccountPayload = {
        name: single.name,
        type: single.type,
        balance: single.balance,
        currency: single.currency.toUpperCase(),
        color: single.color,
        icon: single.icon
    };

    try {
        loaderShow();
        let res;
        if (flag.value === 'insert') {
            res = await accountStore.create(payload);
        } else {
            res = await accountStore.update(single.idHash, payload);
        }

        modalForm.value?.hide();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data akun berhasil disimpan.'
        });

        fetchAccounts();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(idHash: string) {
    Swal.fire({
        title: 'Hapus Akun?',
        text: "Akun ini akan dihapus secara permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await accountStore.destroy(idHash);
                toast.success('Akun berhasil dihapus');
                fetchAccounts();
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
    single.name = '';
    single.type = 'cash';
    single.balance = 0;
    single.currency = 'IDR';
    single.color = '#1abc9c';
    single.icon = '';
}

function formatCurrency(amount: number, currency: string) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: currency }).format(amount);
}

</script>
