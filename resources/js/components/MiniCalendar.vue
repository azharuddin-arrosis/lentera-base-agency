<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: string; // Y-m-d
        min?: string; // Y-m-d
    }>(),
    { min: '' },
);
const emit = defineEmits<{ 'update:modelValue': [string] }>();

const WEEKDAYS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
const pad = (n: number) => String(n).padStart(2, '0');

const base = props.modelValue ? new Date(props.modelValue + 'T00:00:00') : new Date();
const viewYear = ref(base.getFullYear());
const viewMonth = ref(base.getMonth());

const monthLabel = computed(() => new Date(viewYear.value, viewMonth.value, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }));
const minReached = computed(() => {
    if (!props.min) return false;
    const first = `${viewYear.value}-${pad(viewMonth.value + 1)}-01`;
    return first <= props.min && new Date(props.min + 'T00:00:00').getMonth() === viewMonth.value && new Date(props.min + 'T00:00:00').getFullYear() === viewYear.value;
});

const prev = () => {
    if (minReached.value) return;
    if (viewMonth.value === 0) { viewMonth.value = 11; viewYear.value--; } else viewMonth.value--;
};
const next = () => {
    if (viewMonth.value === 11) { viewMonth.value = 0; viewYear.value++; } else viewMonth.value++;
};

const cells = computed(() => {
    const y = viewYear.value;
    const m = viewMonth.value;
    const offset = (new Date(y, m, 1).getDay() + 6) % 7;
    const days = new Date(y, m + 1, 0).getDate();
    const out: Array<{ date: string | null; day: number | null; weekend: boolean }> = [];
    for (let i = 0; i < offset; i++) out.push({ date: null, day: null, weekend: false });
    for (let d = 1; d <= days; d++) {
        const wd = new Date(y, m, d).getDay();
        out.push({ date: `${y}-${pad(m + 1)}-${pad(d)}`, day: d, weekend: wd === 0 || wd === 6 });
    }
    while (out.length % 7 !== 0) out.push({ date: null, day: null, weekend: false });
    return out;
});

const disabled = (date: string) => !!props.min && date < props.min;
const select = (date: string | null) => { if (date && !disabled(date)) emit('update:modelValue', date); };
</script>

<template>
    <div class="rounded-xl border border-border/60 bg-background p-3">
        <div class="mb-2 flex items-center justify-between">
            <span class="text-sm font-semibold capitalize">{{ monthLabel }}</span>
            <div class="flex gap-1">
                <button type="button" class="inline-flex size-7 items-center justify-center rounded-lg border border-border hover:bg-muted disabled:opacity-30" :disabled="minReached" @click="prev"><ChevronLeft class="size-4" /></button>
                <button type="button" class="inline-flex size-7 items-center justify-center rounded-lg border border-border hover:bg-muted" @click="next"><ChevronRight class="size-4" /></button>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <div v-for="d in WEEKDAYS" :key="d" class="pb-1 text-center text-[10px] font-medium text-muted-foreground">{{ d }}</div>
            <template v-for="(c, i) in cells" :key="i">
                <div v-if="!c.date" class="aspect-square"></div>
                <button
                    v-else
                    type="button"
                    :disabled="disabled(c.date)"
                    class="flex aspect-square items-center justify-center rounded-lg text-sm transition-colors"
                    :class="[
                        modelValue === c.date ? 'bg-emerald-500 font-semibold text-white'
                        : disabled(c.date) ? 'cursor-not-allowed text-muted-foreground/30'
                        : c.weekend ? 'text-muted-foreground hover:bg-muted' : 'hover:bg-emerald-500/10',
                    ]"
                    @click="select(c.date)"
                >
                    {{ c.day }}
                </button>
            </template>
        </div>
    </div>
</template>
