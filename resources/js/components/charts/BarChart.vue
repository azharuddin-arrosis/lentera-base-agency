<script setup lang="ts">
import { computed } from 'vue';
import { formatUsd } from '@/lib/format';

interface Point {
    label: string;
    value: number;
}

const props = withDefaults(
    defineProps<{
        points: Point[];
        height?: number;
        mask?: boolean;
    }>(),
    { height: 200, mask: false },
);

const geo = computed(() => {
    const pts = props.points ?? [];
    const H = props.height;
    const values = pts.map((p) => p.value);
    const min = Math.min(0, ...values);
    const max = Math.max(0, ...values);
    const range = max - min || 1;
    const zeroY = (max / range) * H;

    return pts.map((p) => {
        const h = (Math.abs(p.value) / range) * H;
        const top = p.value >= 0 ? zeroY - h : zeroY;
        return { ...p, h: Math.max(h, 2), top, positive: p.value >= 0 };
    });
});
</script>

<template>
    <div class="w-full">
        <div v-if="geo.length" class="flex items-end gap-2 sm:gap-3" :style="{ height: `${height}px` }">
            <div v-for="(bar, i) in geo" :key="i" class="group relative flex flex-1 flex-col justify-end" :style="{ height: `${height}px` }">
                <div
                    class="pointer-events-none absolute -top-1 left-1/2 z-10 -translate-x-1/2 -translate-y-full whitespace-nowrap rounded-md bg-foreground px-2 py-1 text-xs font-medium text-background opacity-0 shadow transition-opacity group-hover:opacity-100"
                >
                    <span v-if="mask" class="select-none blur-[4px]">{{ formatUsd(bar.value, true) }}</span>
                    <span v-else>{{ formatUsd(bar.value, true) }}</span>
                </div>
                <div
                    class="w-full rounded-t-md transition-all"
                    :class="bar.positive ? 'bg-emerald-500/80 group-hover:bg-emerald-500' : 'bg-rose-500/80 group-hover:bg-rose-500'"
                    :style="{ height: `${bar.h}px`, marginTop: `${bar.top}px` }"
                />
            </div>
        </div>
        <div class="mt-2 flex gap-2 sm:gap-3">
            <div v-for="(bar, i) in geo" :key="i" class="flex-1 truncate text-center text-[10px] text-muted-foreground sm:text-xs">
                {{ bar.label }}
            </div>
        </div>
    </div>
</template>
