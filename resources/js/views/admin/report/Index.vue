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

                            <div class="card-toolbar">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4 me-1"
                                            :class="{ active: currentTab === 'monthly' }" data-bs-toggle="tab" href="#tab_monthly" @click="currentTab = 'monthly'">
                                            Bulanan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                                            :class="{ active: currentTab === 'yearly' }" data-bs-toggle="tab" href="#tab_yearly" @click="currentTab = 'yearly'">
                                            Tahunan
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="tab-content">
                                <!-- TAB MONTHLY -->
                                <div class="tab-pane fade" :class="{ 'show active': currentTab === 'monthly' }" id="tab_monthly">
                                    <div class="d-flex w-25 mb-5">
                                        <input type="month" class="form-control" v-model="selectedMonth" @change="fetchData">
                                    </div>

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
                                <!-- END TAB MONTHLY -->

                                <!-- TAB YEARLY -->
                                <div class="tab-pane fade" :class="{ 'show active': currentTab === 'yearly' }" id="tab_yearly">
                                    <div class="d-flex w-25 mb-5">
                                        <select class="form-select" v-model="selectedYear" @change="fetchData">
                                            <option v-for="y in availableYears" :key="y" :value="y">{{ y }}</option>
                                        </select>
                                    </div>

                                    <div class="card card-flush border border-dashed rounded px-6 py-6 mb-8">
                                        <h4 class="fw-bolder text-gray-800 mb-5">Ringkasan Arus Kas Tahunan (Tahun {{ selectedYear }})</h4>
                                        <div v-if="yearlySeries.length > 0">
                                            <apexchart type="bar" height="350" :options="yearlyOptions" :series="yearlySeries"></apexchart>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th class="w-10px pe-2 text-center">Bulan</th>
                                                    <th class="min-w-100px text-end">Pemasukan</th>
                                                    <th class="min-w-100px text-end">Pengeluaran</th>
                                                    <th class="min-w-100px text-end">Arus Kas Bersih</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-bold">
                                                <tr v-for="(item, index) in yearlyData" :key="index">
                                                    <td class="text-center">{{ getMonthName(item.month) }}</td>
                                                    <td class="text-end text-success">{{ formatCurrency(item.income) }}</td>
                                                    <td class="text-end text-danger">{{ formatCurrency(item.expense) }}</td>
                                                    <td class="text-end" :class="item.income - item.expense < 0 ? 'text-danger' : 'text-primary'">
                                                        {{ formatCurrency(item.income - item.expense) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- END TAB YEARLY -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useReportStore } from "@stores/report";
import { axiosHandleError, loaderHide, loaderShow } from '@/plugins/global';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

const reportStore = useReportStore();
const currentTab = ref('monthly');
const selectedMonth = ref(dayjs().format('YYYY-MM'));
const selectedYear = ref(dayjs().format('YYYY'));
const availableYears = ref(Array.from({ length: 5 }, (_, i) => String(dayjs().year() - i)));

const monthlyData = ref({ income: 0, expense: 0, balance: 0 });
const categoryExpenses = ref<any[]>([]);
const yearlyData = ref<any[]>([]);

const yearlySeries = computed(() => {
    if (yearlyData.value.length === 0) return [];
    return [
        {
            name: 'Pemasukan',
            data: yearlyData.value.map(y => y.income)
        },
        {
            name: 'Pengeluaran',
            data: yearlyData.value.map(y => y.expense)
        }
    ];
});

const yearlyOptions = computed(() => {
    return {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: yearlyData.value.map(y => getMonthName(y.month)),
        },
        yaxis: {
            title: { text: '(Rupiah)' }
        },
        fill: { opacity: 1 },
        colors: ['#50CD89', '#F1416C'],
        tooltip: {
            y: {
                formatter: function (val: number) {
                    return formatCurrency(val);
                }
            }
        }
    };
});

onMounted(() => {
    fetchData();
});

async function fetchData() {
    try {
        loaderShow();
        const yearM = selectedMonth.value.split('-')[0];
        const monthM = selectedMonth.value.split('-')[1];

        if (currentTab.value === 'monthly' || currentTab.value === 'both' || !yearlyData.value.length) {
            const resMonthly = await reportStore.getMonthly(yearM, monthM);
            monthlyData.value = resMonthly.data.data;

            const resCat = await reportStore.getCategoryExpense(yearM, monthM);
            categoryExpenses.value = resCat.data.data;
        }

        const resYearly = await reportStore.getYearly(selectedYear.value);
        yearlyData.value = resYearly.data.data;

    } catch (e) {
        axiosHandleError(e);
    } finally {
        loaderHide();
    }
}

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}

function getMonthName(monthNumber: number) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return months[monthNumber - 1] || '';
}
</script>
