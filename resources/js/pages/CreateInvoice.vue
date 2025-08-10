<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Invoice from '@/components/invoice/Invoice.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create Invoice',
        href: '/createinvoice',
    },
];
</script>

<template>
    <Head title="Create Invoice" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <Invoice/>
        </div>
    </AppLayout>
</template>
