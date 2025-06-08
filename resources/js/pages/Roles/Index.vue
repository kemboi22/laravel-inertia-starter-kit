<script setup lang="ts">
import DataTable, { PaginationData, type Column } from '@/components/data/DataTable.vue';
import { Plus } from 'lucide-vue-next';
const breadcrumbs = [{ title: 'Dashboard', href: route('dashboard') }, { title: 'Roles' }];
interface Role {
    id: number;
    name: string;
}
interface Props {
    roles: Role[];
    pagination: PaginationData;
    filters: {
        search?: string;
        sort_column?: string;
        sort_direction?: string;
    };
}
const props = defineProps<Props>();
const columns: Column[] = [
    {
        key: 'name',
        label: 'Name',
        sortable: true,
        searchable: true,
    },
];
</script>
<template>
    <DefaultLayout title="All Roles" :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Roles</h1>
                <Button as="a" :href="route('users.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Role
                </Button>
            </div>
            <DataTable :columns="columns" :data="roles"> </DataTable>
        </div>
    </DefaultLayout>
</template>
