<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLogo from '@/components/common/AppLogo.vue';

interface Props {
    title?: string;
    description?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Authentication',
    description: '',
});
</script>

<template>
    <div>
        <Head :title="title" />
        
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <div class="flex justify-center">
                    <AppLogo class="h-12 w-auto" />
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    {{ title }}
                </h2>
                <p v-if="description" class="mt-2 text-center text-sm text-gray-600">
                    {{ description }}
                </p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
