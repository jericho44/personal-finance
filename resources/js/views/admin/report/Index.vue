<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    
                    <div class="card card-flush mt-5 mb-5 mb-xl-10">
                        <div class="card-header border-0 pt-5 align-items-center">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark mb-2">Laporan Keuangan</span>
                                <span class="text-muted fs-6">Analisis Arus Kas dan Pengeluaran</span>
                            </h3>

                            <div class="d-flex w-25">
                                <input type="month" class="form-control" v-model="selectedMonth" @change="fetchData">
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="row g-5 mb-5">
                                <div class="col-md-4">
                                    <div class="card bg-light-success py-4 px-6 rounded border border-success border-dashed">
                                        <div class="text-success fs-2 fw-bolder mb-1">{{ formatCurrency(monthlyData.income) }}</div>
                                        <div class="text-gray-600 fw-bold fs-6">Pemasukan Bulan Ini</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light-danger py-4 px-6 rounded border border-danger border-dashed">
                                        <div class="text-danger fs-2 fw-bolder mb-1">{{ formatCurrency(monthlyData.expense) }}</div>
                                        <div class="text-gray-600 fw-bold fs-6">Pengeluaran Bulan Ini</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card py-4 px-6 rounded border border-primary border-dashed" :class="monthlyData.balance < 0 ? 'bg-light-danger border-danger' : 'bg-light-primary border-primary'">
                                        <div class="fs-2 fw-bolder mb-1" :class="monthlyData.balance < 0 ? 'text-danger' : 'text-primary'">{{ formatCurrency(monthlyData.balance) }}</div>
                                        <div class="text-gray-600 fw-bold fs-6">Arus Kas (Sisa)</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted my-8">

                            <h4 class="fw-bolder text-gray-800 mb-5">Pengeluaran per Kategori (Bulan {{ dayjs(selectedMonth).format('MMMM YYYY') }})</h4>
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="w-10px pe-2 text-center">No</th>
                                            <th class="min-w-125px">Kategori</th>
                                            <th class="min-w-100px text-end">Total Pengeluaran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        <tr v-if="categoryExpenses.length === 0">
                                            <td colspan="3" class="text-center">Belum ada data pengeluaran di bulan ini.</td>
                                        </tr>
                                        <tr v-for="(cat, index) in categoryExpenses" :key="cat.category_name">
                                            <td class="text-center">{{ index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="bullet bullet-vertical h-15px w-3px me-3" :style="{ backgroundColor: cat.color || '#F64E60' }"></span>
                                                    {{ cat.category_name }}
                                                </div>
                                            </td>
                                            <td class="text-end fw-bolder text-danger">{{ formatCurrency(cat.total) }}</td>
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
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useReportStore } from "@stores/report";
import { axiosHandleError, loaderHide, loaderShow } from '@/plugins/global';
import dayjs from 'dayjs';

const reportStore = useReportStore();
const selectedMonth = ref(dayjs().format('YYYY-MM'));
const monthlyData = ref({ income: 0, expense: 0, balance: 0 });
const categoryExpenses = ref<any[]>([]);

onMounted(() => {
    fetchData();
});

async function fetchData() {
    try {
        loaderShow();
        const year = selectedMonth.value.split('-')[0];
        const month = selectedMonth.value.split('-')[1];

        const resMonthly = await reportStore.getMonthly(year, month);
        monthlyData.value = resMonthly.data.data;

        const resCat = await reportStore.getCategoryExpense(year, month);
        categoryExpenses.value = resCat.data.data;

    } catch (e) {
        axiosHandleError(e);
    } finally {
        loaderHide();
    }
}

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}
</script>
