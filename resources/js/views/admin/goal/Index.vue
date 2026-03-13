<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card card-flush mt-5 mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-5 align-items-center">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark mb-2">Tujuan Keuangan</span>
                                    <span class="text-muted fs-6">Kelola dan Pantau Pencapaian Finansial Anda</span>
                                </h3>
                                <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                    Tambah Tujuan Baru
                                </button>
                            </div>
                            <div class="card-body pt-5">
                                <div class="row g-5">
                                    <div class="col-md-4" v-for="goal in goals" :key="goal.idHash">
                                        <div class="card border border-2 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-grow-1">
                                                    <a href="javascript:;" class="text-gray-900 text-hover-primary fw-bolder fs-4">{{ goal.name }}</a>
                                                </div>
                                                <div>
                                                    <span v-if="goal.isCompleted" class="badge badge-success">Tercapai</span>
                                                    <span v-else class="badge badge-primary">Berjalan</span>
                                                </div>
                                            </div>
                                            
                                            <div class="fs-6 fw-bold text-gray-500 mb-5">
                                                Terkumpul: {{ formatCurrency(goal.currentAmount) }} / {{ formatCurrency(goal.targetAmount) }}
                                                <div class="fs-7 text-muted mt-2" v-if="goal.deadline">Batas: {{ formatDate(goal.deadline) }}</div>
                                            </div>

                                            <div class="progress h-6px mb-3">
                                                <div class="progress-bar" :style="`background-color: ${goal.color || '#148FFF'}; width: ${Math.min((goal.currentAmount / goal.targetAmount) * 100, 100)}%;`"></div>
                                            </div>
                                            <div class="fs-8 text-gray-400 mb-5">{{ ((goal.currentAmount / goal.targetAmount) * 100).toFixed(2) }}% Tercapai</div>

                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="edit(goal)">
                                                    <span class="svg-icon svg-icon-primary"><i class="fa fa-pen" /></span>
                                                </button>
                                                <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="confirmDelete(goal.idHash)">
                                                    <span class="svg-icon svg-icon-danger"><i class="fa fa-trash" /></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" v-if="goals.length === 0">
                                        <div class="text-center text-muted">Belum ada tujuan keuangan yang ditambahkan.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomModal ref="modalForm" :title="flag === 'insert' ? 'Tambah Tujuan Finansial' : 'Edit Tujuan Finansial'" :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="modal-lg">
            <ModalBody>
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.name.$error }">Nama Tujuan</label>
                        <input class="form-control" type="text" placeholder="Misal: Beli Rumah" v-model="single.name">
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Warna (Opsional)</label>
                        <input class="form-control form-control-color" type="color" v-model="single.color">
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6" :class="{ 'text-danger': v$.single.target_amount.$error }">Target Dana</label>
                        <input class="form-control" type="number" placeholder="0" v-model="single.target_amount" step="0.01">
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Dana Terkumpul Saat Ini</label>
                        <input class="form-control" type="number" placeholder="0" v-model="single.current_amount" step="0.01">
                    </div>
                    
                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Batas Waktu (Opsional)</label>
                        <input class="form-control" type="date" v-model="single.deadline">
                    </div>

                    <div class="col-md-6 mb-5">
                        <label class="form-label fw-bolder fs-6">Status Penyelesaian</label>
                        <select class="form-select" v-model="single.is_completed">
                            <option :value="false">Belum Tercapai / Berjalan</option>
                            <option :value="true">Sudah Tercapai</option>
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
import { useGoalStore } from "@stores/goal";
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, minValue } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, loaderHide, loaderShow } from '@/plugins/global';
import { toast } from 'vue3-toastify';
import dayjs from 'dayjs';

const goalStore = useGoalStore();
const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);
const goals = ref<any[]>([]);
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    idHash: '',
    name: '',
    target_amount: '',
    current_amount: '0',
    deadline: '',
    color: '#148FFF',
    is_completed: false,
    notes: '',
});

const rules = computed(() => ({
    single: {
        name: { required },
        target_amount: { required, numeric, minValue: minValue(0.01) },
    }
}));
const v$ = useVuelidate(rules, { single });

onMounted(async () => {
    await fetchGoals();
});

async function fetchGoals() {
    try {
        loaderShow();
        const res = await goalStore.getAll();
        goals.value = res.data.data;
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
    single.target_amount = item.targetAmount;
    single.current_amount = item.currentAmount;
    single.deadline = item.deadline ? dayjs(item.deadline).format('YYYY-MM-DD') : '';
    single.color = item.color || '#148FFF';
    single.is_completed = item.isCompleted;
    single.notes = item.notes || '';
    modalForm.value?.show();
}

async function saveData() {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    const payload: any = {
        name: single.name,
        target_amount: Number(single.target_amount),
        current_amount: Number(single.current_amount) || 0,
        deadline: single.deadline || null,
        color: single.color,
        is_completed: single.is_completed ? 1 : 0,
        notes: single.notes
    };

    try {
        loaderShow();
        if (flag.value === 'insert') {
            await goalStore.create(payload);
        } else {
            await goalStore.update(single.idHash, payload);
        }
        modalForm.value?.hide();
        toast.success('Tujuan Finansial berhasil disimpan.');
        fetchGoals();
    } catch (error) {
        axiosHandleError(error);
    } finally {
        loaderHide();
    }
}

async function confirmDelete(idHash: string) {
    Swal.fire({
        title: 'Hapus Tujuan Finansial?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F64E60',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow();
                await goalStore.destroy(idHash);
                toast.success('Berhasil dihapus');
                fetchGoals();
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
    single.target_amount = '';
    single.current_amount = '0';
    single.deadline = '';
    single.color = '#148FFF';
    single.is_completed = false;
    single.notes = '';
}

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}

function formatDate(date: string) {
    return dayjs(date).format('DD MMM YYYY');
}
</script>
