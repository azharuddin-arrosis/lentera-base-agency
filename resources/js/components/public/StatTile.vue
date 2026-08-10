<script setup lang="ts">
withDefaults(
    defineProps<{
        label: string;
        value: string;
        sub?: string;
        accent?: 'default' | 'up' | 'down';
    }>(),
    { accent: 'default' },
);
</script>

<template>
    <div class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">{{ label }}</p>
        <p
            class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl"
            :class="{
                'text-emerald-600 dark:text-emerald-400': accent === 'up',
                'text-rose-600 dark:text-rose-400': accent === 'down',
            }"
        >
            <slot name="value">{{ value }}</slot>
        </p>
        <p v-if="sub" class="mt-1 text-xs text-muted-foreground">{{ sub }}</p>
    </div>
</template>
