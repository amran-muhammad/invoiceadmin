<script setup lang="ts">
import CustomerListComponent from '@/components/customerlist/CustomerListComponent.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customer List',
        href: '/customerlist',
    },
];
</script>

<template>
    <Head title="Product List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <CustomerListComponent />
        </div>
    </AppLayout>
</template>
