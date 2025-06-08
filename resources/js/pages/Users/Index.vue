<script setup lang="ts">
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import DataTable, { type Column, type PaginationData } from '@/components/data/DataTable.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Plus, Eye, Edit, Trash } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    roles: Array<{ id: number; name: string }>;
    created_at: string;
}

interface Props {
    users: User[];
    pagination: PaginationData;
    filters: {
        search?: string;
        sort_column?: string;
        sort_direction?: string;
    };
}

const props = defineProps<Props>();

const loading = ref(false);

const columns: Column[] = [
    {
        key: 'name',
        label: 'Name',
        sortable: true,
        searchable: true,
    },
    {
        key: 'email',
        label: 'Email',
        sortable: true,
        searchable: true,
    },
    {
        key: 'roles',
        label: 'Roles',
        sortable: false,
    },
    {
        key: 'created_at',
        label: 'Created',
        sortable: true,
        render: (value: string) => new Date(value).toLocaleDateString(),
    },
    {
        key: 'actions',
        label: 'Actions',
        sortable: false,
    },
];

const handleSearch = (query: string) => {
    loading.value = true;
    router.get(route('users.index'), 
        { search: query, sort_column: props.filters.sort_column, sort_direction: props.filters.sort_direction },
        { preserveState: true, onFinish: () => loading.value = false }
    );
};

const handleSort = (column: string, direction: 'asc' | 'desc') => {
    loading.value = true;
    router.get(route('users.index'),
        { search: props.filters.search, sort_column: column, sort_direction: direction },
        { preserveState: true, onFinish: () => loading.value = false }
    );
};

const handlePaginate = (page: number) => {
    loading.value = true;
    router.get(route('users.index'),
        { ...props.filters, page },
        { preserveState: true, onFinish: () => loading.value = false }
    );
};

const handlePerPageChange = (perPage: number) => {
    loading.value = true;
    router.get(route('users.index'),
        { ...props.filters, per_page: perPage },
        { preserveState: true, onFinish: () => loading.value = false }
    );
};

const deleteUser = (user: User) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', user.id));
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Users' },
];
</script>

<template>
    <DefaultLayout title="Users" :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Users</h1>
                <Button as="a" :href="route('users.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Add User
                </Button>
            </div>
            
            <DataTable
                :data="users"
                :columns="columns"
                :pagination="pagination"
                :loading="loading"
                title="All Users"
                @search="handleSearch"
                @sort="handleSort"
                @paginate="handlePaginate"
                @per-page-change="handlePerPageChange"
            >
                <template #cell.roles="{ value }">
                    <div class="flex flex-wrap gap-1">
                        <Badge v-for="role in value" :key="role.id" variant="secondary">
                            {{ role.name }}
                        </Badge>
                    </div>
                </template>
                
                <template #cell.actions="{ row }">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="sm">
                                <MoreHorizontal class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem as="a" :href="route('users.show', row.id)">
                                <Eye class="mr-2 h-4 w-4" />
                                View
                            </DropdownMenuItem>
                            <DropdownMenuItem as="a" :href="route('users.edit', row.id)">
                                <Edit class="mr-2 h-4 w-4" />
                                Edit
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="deleteUser(row)" class="text-destructive">
                                <Trash class="mr-2 h-4 w-4" />
                                Delete
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </template>
            </DataTable>
        </div>
    </DefaultLayout>
</template>

