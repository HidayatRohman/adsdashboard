<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Line, Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, BarElement, ArcElement } from 'chart.js';
import { Users, BadgeCheckIcon, Wallet, TrendingUp } from 'lucide-vue-next';
import { format, subDays } from 'date-fns';
import { id } from 'date-fns/locale';

// Register ChartJS components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Period selectors for charts
const period = ref('7'); // 7, 30, or 90 days

// Mock data - Replace with actual API data later
const stats = ref({
    totalLeads: 1250,
    newLeadsToday: 24,
    closingRate: 35,
    totalClosing: 48,
    totalRevenue: 185000000,
    revenueToday: 12500000,
    avgTransactionValue: 3850000,
    totalTransactions: 48
});

// Chart Options
const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: number) => formatCurrency(value)
            }
        }
    },
    plugins: {
        legend: {
            position: 'bottom' as const
        }
    }
};

const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const
        }
    }
};

const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true
        }
    },
    plugins: {
        legend: {
            position: 'bottom' as const
        }
    }
};

// Generate dates for the last N days
const generateDates = (days: number) => {
    return Array.from({ length: days }, (_, i) => {
        return format(subDays(new Date(), days - 1 - i), 'dd MMM', { locale: id });
    });
};

// Mock chart data - Replace with actual API data later
const revenueData = ref({
    labels: generateDates(7),
    datasets: [{
        label: 'Pendapatan',
        data: [65000000, 75000000, 85000000, 82000000, 90000000, 95000000, 88000000],
        borderColor: '#0ea5e9',
        backgroundColor: '#0ea5e9',
        tension: 0.4
    }]
});

const leadSourceData = ref({
    labels: ['Facebook', 'Instagram', 'TikTok', 'Google', 'Referral'],
    datasets: [{
        data: [300, 250, 200, 150, 100],
        backgroundColor: [
            '#0ea5e9',
            '#f43f5e',
            '#a855f7',
            '#22c55e',
            '#eab308'
        ]
    }]
});

const marketingData = ref({
    labels: ['John', 'Jane', 'Bob', 'Alice', 'Charlie'],
    datasets: [
        {
            label: 'Leads',
            data: [65, 59, 80, 81, 56],
            backgroundColor: '#0ea5e9'
        },
        {
            label: 'Closing',
            data: [28, 25, 35, 39, 22],
            backgroundColor: '#22c55e'
        }
    ]
});

const productData = ref({
    labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
    datasets: [{
        label: 'Penjualan',
        data: [120, 190, 300, 250, 200],
        backgroundColor: '#0ea5e9'
    }]
});

// Format number to IDR currency
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// Update chart data when period changes
const updateChartData = () => {
    // Here you would normally fetch new data from the API
    // For now, we'll just update the labels
    revenueData.value.labels = generateDates(Number(period.value));
};

onMounted(() => {
    // Here you would normally fetch initial data from the API
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Summary Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center gap-2">
                        <Users class="h-4 w-4 text-gray-500" />
                        <h3 class="text-sm font-medium">Total Leads</h3>
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ stats.totalLeads }}</p>
                    <p class="text-xs text-gray-500">+{{ stats.newLeadsToday }} hari ini</p>
                </div>
                
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center gap-2">
                        <BadgeCheckIcon class="h-4 w-4 text-gray-500" />
                        <h3 class="text-sm font-medium">Closing Rate</h3>
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ stats.closingRate }}%</p>
                    <p class="text-xs text-gray-500">{{ stats.totalClosing }} closing bulan ini</p>
                </div>

                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center gap-2">
                        <Wallet class="h-4 w-4 text-gray-500" />
                        <h3 class="text-sm font-medium">Pendapatan</h3>
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ formatCurrency(stats.totalRevenue) }}</p>
                    <p class="text-xs text-gray-500">+{{ formatCurrency(stats.revenueToday) }} hari ini</p>
                </div>

                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center gap-2">
                        <TrendingUp class="h-4 w-4 text-gray-500" />
                        <h3 class="text-sm font-medium">Rata-rata Transaksi</h3>
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ formatCurrency(stats.avgTransactionValue) }}</p>
                    <p class="text-xs text-gray-500">dari {{ stats.totalTransactions }} transaksi</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Revenue Trend -->
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Trend Pendapatan</h3>
                        <select 
                            v-model="period" 
                            @change="updateChartData"
                            class="rounded-lg border px-2 py-1 text-sm dark:bg-sidebar-background dark:border-sidebar-border"
                        >
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <Line
                        :data="revenueData"
                        :options="lineOptions"
                        class="h-[300px]"
                    />
                </div>

                <!-- Lead Sources -->
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Sumber Lead</h3>
                        <select 
                            v-model="period"
                            class="rounded-lg border px-2 py-1 text-sm dark:bg-sidebar-background dark:border-sidebar-border"
                        >
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <Pie
                        :data="leadSourceData"
                        :options="pieOptions"
                        class="h-[300px]"
                    />
                </div>

                <!-- Marketing Performance -->
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Performa Marketing</h3>
                        <select 
                            v-model="period"
                            class="rounded-lg border px-2 py-1 text-sm dark:bg-sidebar-background dark:border-sidebar-border"
                        >
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <Bar
                        :data="marketingData"
                        :options="barOptions"
                        class="h-[300px]"
                    />
                </div>

                <!-- Product Performance -->
                <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-sidebar-background dark:border-sidebar-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Produk Terlaris</h3>
                        <select 
                            v-model="period"
                            class="rounded-lg border px-2 py-1 text-sm dark:bg-sidebar-background dark:border-sidebar-border"
                        >
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <Bar
                        :data="productData"
                        :options="barOptions"
                        class="h-[300px]"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
