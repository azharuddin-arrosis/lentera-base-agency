<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Lock } from '@lucide/vue';
import { computed } from 'vue';
import { formatUsd, pnlColor } from '@/lib/format';

const props = withDefaults(
    defineProps<{
        value: number;
        withSign?: boolean;
        colored?: boolean;
        showLock?: boolean;
    }>(),
    { withSign: false, colored: true, showLock: true },
);

const page = usePage();
const authed = computed(() => !!(page.props.auth as { user?: unknown } | undefined)?.user);

const text = computed(() => formatUsd(props.value, props.withSign));
const colorClass = computed(() => (props.colored ? pnlColor(props.value) : ''));
</script>

<template>
    <span v-if="authed" :class="colorClass">{{ text }}</span>
    <span v-else class="inline-flex items-center gap-1 align-middle" title="Login untuk melihat angka">
        <span class="select-none blur-[5px]" aria-hidden="true" :class="colorClass">{{ text }}</span>
        <Lock v-if="showLock" class="size-3 shrink-0 text-muted-foreground" />
    </span>
</template>
