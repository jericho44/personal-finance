<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        
                        <!-- Header & Net Worth -->
                        <div class="row g-5 g-xl-8 mb-xl-5 mb-5">
                            <div class="col-xl-12">
                                <div class="card card-flush bg-primary text-white pt-5 pb-5">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h4 class="text-white opacity-75 mb-2">Total Saldo Bersih</h4>
                                                <h1 class="text-white fw-bolder fs-2tx mb-0">{{ formatCurrency(summary?.net_worth || 0) }}</h1>
                                            </div>
                                            <div class="symbol symbol-75px symbol-circle bg-white bg-opacity-10 shadow">
                                                <div class="symbol-label">
                                                    <i class="fa fa-wallet text-white fs-1"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Income vs Expense Cards -->
                        <div class="row g-5 g-xl-8 mb-xl-5 mb-5">
                            <div class="col-md-6">
                                <div class="card card-flush h-md-100 bg-light-success border border-success border-dashed">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div class="d-flex flex-column">
                                            <span class="text-success fw-bolder fs-4 mb-1">Pemasukan Bulan Ini</span>
                                            <span class="text-dark fw-bold fs-2">{{ formatCurrency(summary?.current_month?.income || 0) }}</span>
                                        </div>
                                        <span class="svg-icon svg-icon-muted svg-icon-3x svg-icon-success">
                                            <i class="fa fa-arrow-down text-success fs-2x"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-flush h-md-100 bg-light-danger border border-danger border-dashed">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div class="d-flex flex-column">
                                            <span class="text-danger fw-bolder fs-4 mb-1">Pengeluaran Bulan Ini</span>
                                            <span class="text-dark fw-bold fs-2">{{ formatCurrency(summary?.current_month?.expense || 0) }}</span>
                                        </div>
                                        <span class="svg-icon svg-icon-muted svg-icon-3x svg-icon-danger">
                                            <i class="fa fa-arrow-up text-danger fs-2x"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts and Recent Transactions -->
                        <div class="row g-5 g-xl-8">
                            <!-- Donut Chart -->
                            <div class="col-xl-6">
                                <div class="card card-flush h-md-100">
                                    <div class="card-header pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bolder text-dark">Pengeluaran per Kategori</span>
                                            <span class="text-gray-400 mt-1 fw-bold fs-6">Bulan Ini</span>
                                        </h3>
                                    </div>
                                    <div class="card-body pt-5">
                                        <div v-if="(summary?.spending_by_category?.length || 0) > 0" class="d-flex flex-center">
                                            <apexchart type="donut" width="100%" height="300" :options="chartOptions" :series="chartSeries"></apexchart>
                                        </div>
                                        <div v-else class="text-center py-10">
                                            <span class="text-muted fs-5">Belum ada data pengeluaran bulan ini.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Transactions -->
                            <div class="col-xl-6">
                                <div class="card card-flush h-md-100">
                                    <div class="card-header pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bolder text-dark">Transaksi Terakhir</span>
                                        </h3>
                                        <div class="card-toolbar">
                                            <router-link :to="{ name: 'a-transactions' }" class="btn btn-sm btn-light-primary">Lihat Semua</router-link>
                                        </div>
                                    </div>
                                    <div class="card-body pt-5">
                                        <div v-if="(summary?.recent_transactions?.length || 0) > 0" class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                                <tbody class="text-gray-600 fw-bold">
                                                    <tr v-for="tx in summary?.recent_transactions" :key="tx.id_hash">
                                                        <td class="w-50px">
                                                            <div class="symbol symbol-40px symbol-circle" :style="`background-color: ${tx.category?.color || '#f5f8fa'}33`">
                                                                <span class="symbol-label">
                                                                    <i :class="`${tx.category?.icon || 'fa fa-money-bill'} fs-4`" :style="`color: ${tx.category?.color || '#a1a5b7'}`"></i>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="text-dark fw-bolder">{{ tx.category?.name || 'Uncategorized' }}</span>
                                                                <span class="text-muted fs-7">{{ tx.account?.name }} {{ tx.targetAccount ? '➜ ' + tx.targetAccount.name : '' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <span :class="{
                                                                'text-success fw-bolder': tx.type === 'income',
                                                                'text-danger fw-bolder': tx.type === 'expense',
                                                                'text-primary fw-bolder': tx.type === 'transfer'
                                                            }">
                                                                {{ tx.type === 'income' ? '+' : (tx.type === 'expense' ? '-' : '') }}{{ formatCurrency(tx.amount) }}
                                                            </span>
                                                            <div class="text-muted fs-8">{{ formatDate(tx.date) }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div v-else class="text-center py-10">
                                            <span class="text-muted fs-5">Belum ada transaksi.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useDashboardStore } from "@stores/dashboard";
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from "@/plugins/global";
import { IDashboardData } from "@/types/dashboard";
import dayjs from "dayjs";

const dashboardStore = useDashboardStore();
const summary = ref<IDashboardData | null>(null);

const chartSeries = computed(() => {
    if (!summary.value || !summary.value.spending_by_category) return [];
    return summary.value.spending_by_category.map(item => Number(item.total));
});

const chartOptions = computed(() => {
    const categories = summary.value?.spending_by_category || [];
    return {
        chart: {
            type: 'donut',
            fontFamily: 'inherit',
        },
        labels: categories.map(item => item.category),
        colors: categories.map(item => item.color || '#cccccc'),
        stroke: {
            width: 0
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: { show: true },
                        value: {
                            show: true,
                            formatter: function (val: string) {
                                return formatCurrency(Number(val));
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            formatter: function (w: any) {
                                const total = w.globals.seriesTotals.reduce((a: number, b: number) => a + b, 0);
                                return formatCurrency(total);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: true,
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function (val: number) {
                    return formatCurrency(val);
                }
            }
        }
    };
});

onMounted(async () => {
    initializeAppPlugins();
    loaderShow()
    await getData();
    loaderHide();
});

const getData = async () => {
    try {
        const res = await dashboardStore.fetchSummary();
        summary.value = res.data.data;
    } catch (error) {
        axiosHandleError(error);
    }
};

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}

function formatDate(date: string) {
    return dayjs(date).format('DD MMM YYYY');
}

</script>
