<script setup lang="ts">
import { computed } from 'vue';

interface Point {
    label: string;
    value: number;
}

const props = withDefaults(
    defineProps<{
        points: Point[];
        height?: number;
        color?: string;
    }>(),
    {
        height: 220,
        color: '#059669',
    },
);

const W = 720;
const PADDING = 8;

const geo = computed(() => {
    const pts = props.points ?? [];
    if (pts.length < 2) {
        return { line: '', area: '', last: null as null | { x: number; y: number }, min: 0, max: 0 };
    }

    const H = props.height;
    const values = pts.map((p) => p.value);
    let min = Math.min(0, ...values);
    let max = Math.max(0, ...values);
    if (min === max) {
        max = min + 1;
    }
    const range = max - min;

    const x = (i: number) => PADDING + (i / (pts.length - 1)) * (W - PADDING * 2);
    const y = (v: number) => PADDING + (1 - (v - min) / range) * (H - PADDING * 2);

    const coords = pts.map((p, i) => ({ x: x(i), y: y(p.value) }));
    const line = coords.map((c, i) => `${i === 0 ? 'M' : 'L'}${c.x.toFixed(1)},${c.y.toFixed(1)}`).join(' ');
    const area = `${line} L${coords[coords.length - 1].x.toFixed(1)},${(H - PADDING).toFixed(1)} L${coords[0].x.toFixed(1)},${(H - PADDING).toFixed(1)} Z`;
    const zeroY = y(0);

    return { line, area, last: coords[coords.length - 1], min, max, zeroY, H };
});

const gradId = `eq-grad-${Math.random().toString(36).slice(2, 8)}`;
</script>

<template>
    <div class="w-full">
        <svg
            v-if="geo.line"
            :viewBox="`0 0 ${W} ${height}`"
            class="h-auto w-full"
            preserveAspectRatio="none"
            role="img"
            aria-label="Grafik kurva ekuitas"
        >
            <defs>
                <linearGradient :id="gradId" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" :stop-color="color" stop-opacity="0.35" />
                    <stop offset="100%" :stop-color="color" stop-opacity="0" />
                </linearGradient>
            </defs>

            <!-- garis nol -->
            <line
                v-if="geo.zeroY !== undefined"
                :x1="PADDING"
                :x2="W - PADDING"
                :y1="geo.zeroY"
                :y2="geo.zeroY"
                stroke="currentColor"
                stroke-dasharray="4 5"
                class="text-border"
                stroke-width="1"
            />

            <path :d="geo.area" :fill="`url(#${gradId})`" />
            <path :d="geo.line" fill="none" :stroke="color" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
            <circle v-if="geo.last" :cx="geo.last.x" :cy="geo.last.y" r="4" :fill="color" />
        </svg>
        <div v-else class="flex h-40 items-center justify-center text-sm text-muted-foreground">
            Belum ada data untuk ditampilkan.
        </div>
    </div>
</template>
