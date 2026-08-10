<script setup lang="ts">
withDefaults(defineProps<{ label?: string }>(), { label: 'Masuk dengan Google' });
</script>

<template>
    <a
        href="/auth/google/redirect"
        class="flex w-full items-center justify-center gap-2.5 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium shadow-sm transition-colors hover:bg-muted"
    >
        <svg class="size-4.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.2.8 3.9 1.5l2.7-2.6C16.9 2.9 14.7 2 12 2 6.9 2 2.8 6.1 2.8 11.2S6.9 20.4 12 20.4c5.9 0 9.8-4.1 9.8-9.9 0-.7-.1-1.2-.2-1.7H12z" />
        </svg>
        {{ label }}
    </a>
</template>
