<script setup lang="ts">
import ProductListComponent from '@/components/productlist/ProductListComponent.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Product List',
        href: '/productlist',
    },
];
</script>

<template>
    <Head title="Product List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <ProductListComponent />
        </div>
    </AppLayout>
</template>
