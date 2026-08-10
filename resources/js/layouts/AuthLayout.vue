<script setup lang="ts">
import AuthBrandLayout from '@/layouts/auth/AuthBrandLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthBrandLayout :title="title" :description="description">
        <slot />
    </AuthBrandLayout>
</template>
