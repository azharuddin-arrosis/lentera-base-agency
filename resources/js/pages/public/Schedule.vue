<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { CalendarCheck, CheckCircle2, ChevronDown, ChevronLeft, ChevronRight, Clock, Info, Search, Users } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatIdr } from '@/lib/format';

interface Consultant {
    key: string;
    name: string;
    title: string | null;
    avatar_url: string | null;
    rate: number;
}

const props = defineProps<{
    consultants: Consultant[];
    booked: string[]; // "key|Y-m-d|HH:MM"
    today: string;
    hours: Record<number, string[]>;
    adminKey: string;
}>();

const WEEKDAYS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
const bookedSet = new Set(props.booked);

const form = useForm({
    consultant: props.consultants[0]?.key ?? props.adminKey,
    name: '',
    email: '',
    phone: '',
    date: '',
    times: [] as string[],
    notes: '',
});

const selectedConsultant = computed(() => props.consultants.find((c) => c.key === form.consultant) ?? props.consultants[0]);

// reset pilihan jam bila konsultan berganti
watch(() => form.consultant, () => (form.times = []));

// ---- Dropdown pemilih konsultan (dengan pencarian) ----
const pickerOpen = ref(false);
const consultantSearch = ref('');
const filteredConsultants = computed(() => {
    const q = consultantSearch.value.trim().toLowerCase();
    if (!q) return props.consultants;
    return props.consultants.filter(
        (c) => c.name.toLowerCase().includes(q) || (c.title ?? '').toLowerCase().includes(q),
    );
});
const chooseConsultant = (key: string) => {
    form.consultant = key;
    pickerOpen.value = false;
    consultantSearch.value = '';
};

// ---- Kalender ----
const todayDate = new Date(props.today + 'T00:00:00');
const now = new Date();
const nowHHMM = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

const viewYear = ref(todayDate.getFullYear());
const viewMonth = ref(todayDate.getMonth());
const pad = (n: number) => String(n).padStart(2, '0');
const monthLabel = computed(() => new Date(viewYear.value, viewMonth.value, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }));
const isCurrentMonth = computed(() => viewYear.value === todayDate.getFullYear() && viewMonth.value === todayDate.getMonth());

const prevMonth = () => {
    if (isCurrentMonth.value) return;
    if (viewMonth.value === 0) { viewMonth.value = 11; viewYear.value--; } else viewMonth.value--;
};
const nextMonth = () => {
    if (viewMonth.value === 11) { viewMonth.value = 0; viewYear.value++; } else viewMonth.value++;
};

const cells = computed(() => {
    const y = viewYear.value;
    const m = viewMonth.value;
    const offset = (new Date(y, m, 1).getDay() + 6) % 7;
    const days = new Date(y, m + 1, 0).getDate();
    const out: Array<{ date: string | null; day: number | null }> = [];
    for (let i = 0; i < offset; i++) out.push({ date: null, day: null });
    for (let d = 1; d <= days; d++) out.push({ date: `${y}-${pad(m + 1)}-${pad(d)}`, day: d });
    while (out.length % 7 !== 0) out.push({ date: null, day: null });
    return out;
});

const weekdayOf = (date: string) => new Date(date + 'T00:00:00').getDay();
const hasSlots = (date: string) => (props.hours[weekdayOf(date)] ?? []).length > 0;
const isSelectable = (date: string) => date >= props.today && hasSlots(date);
const isToday = (date: string) => date === props.today;

// jumlah slot terpesan untuk konsultan terpilih pada tanggal itu (badge)
const bookingCount = (date: string) => props.booked.filter((s) => s.startsWith(`${form.consultant}|${date}|`)).length;

const selectDate = (date: string | null) => {
    if (!date || !isSelectable(date)) return;
    form.date = date;
    form.times = [];
};

const slots = computed(() => (form.date ? (props.hours[weekdayOf(form.date)] ?? []) : []));
const slotDisabled = (time: string) => {
    if (bookedSet.has(`${form.consultant}|${form.date}|${time}`)) return true;
    if (form.date === props.today && time <= nowHHMM) return true;
    return false;
};
const toggleTime = (time: string) => {
    if (slotDisabled(time)) return;
    const i = form.times.indexOf(time);
    if (i === -1) form.times.push(time);
    else form.times.splice(i, 1);
    form.times.sort();
};

const selectedLabel = computed(() =>
    form.date ? new Date(form.date + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '',
);
const totalPrice = computed(() => (selectedConsultant.value?.rate ?? 0) * form.times.length);

const submit = () => {
    form.post('/schedule', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'phone', 'notes', 'date', 'times'),
    });
};
</script>

<template>
    <Head title="Jadwalkan Konsultasi" />

    <PublicLayout>
        <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                    <CalendarCheck class="size-3.5" /> Booking Konsultasi
                </span>
                <h1 class="mt-5 text-4xl font-extrabold tracking-tight sm:text-5xl">Jadwalkan Konsultasi</h1>
                <p class="mt-4 text-muted-foreground">Pilih konsultan, tanggal, dan jam (boleh lebih dari satu). Biaya dihitung per jam.</p>
            </div>

            <!-- Kartu terpadu: konsultan + tanggal + jam -->
            <div class="mt-10 rounded-3xl border border-border/60 bg-card shadow-sm">
                <!-- 1) Pilih konsultan (header full-width) -->
                <div class="relative border-b border-border/60 p-5 sm:p-6">
                    <p class="mb-2 text-sm font-semibold">1. Pilih konsultan</p>
                    <div class="relative">
                    <button
                        type="button"
                        class="flex w-full items-center gap-3 rounded-2xl border border-border/60 bg-background p-3 text-left transition-colors hover:border-emerald-500/50"
                        :class="pickerOpen && 'border-emerald-500 ring-1 ring-emerald-500/30'"
                        @click="pickerOpen = !pickerOpen"
                    >
                        <img v-if="selectedConsultant?.avatar_url" :src="selectedConsultant.avatar_url" :alt="selectedConsultant.name" class="size-11 shrink-0 rounded-xl object-cover" />
                        <span v-else class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-green-600 text-white">
                            <Users class="size-5" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold">{{ selectedConsultant?.name }}</span>
                            <span class="block truncate text-[11px] text-muted-foreground">{{ selectedConsultant?.title }}</span>
                        </span>
                        <span class="hidden shrink-0 text-xs font-semibold text-emerald-600 dark:text-emerald-400 sm:block">{{ formatIdr(selectedConsultant?.rate) }}/jam</span>
                        <ChevronDown class="size-4 shrink-0 text-muted-foreground transition-transform" :class="pickerOpen && 'rotate-180'" />
                    </button>

                    <!-- panel dropdown -->
                    <div v-if="pickerOpen">
                        <button type="button" class="fixed inset-0 z-40 cursor-default" aria-hidden="true" @click="pickerOpen = false"></button>
                        <div class="absolute left-0 right-0 z-50 mt-2 overflow-hidden rounded-2xl border border-border/60 bg-card shadow-xl">
                            <div class="border-b border-border/60 p-2">
                                <div class="relative">
                                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        v-model="consultantSearch"
                                        type="text"
                                        placeholder="Cari konsultan..."
                                        class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    />
                                </div>
                            </div>
                            <ul class="max-h-72 overflow-y-auto p-1">
                                <li v-for="c in filteredConsultants" :key="c.key">
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 rounded-xl p-2 text-left transition-colors hover:bg-muted"
                                        :class="form.consultant === c.key && 'bg-emerald-500/5'"
                                        @click="chooseConsultant(c.key)"
                                    >
                                        <img v-if="c.avatar_url" :src="c.avatar_url" :alt="c.name" class="size-9 shrink-0 rounded-lg object-cover" />
                                        <span v-else class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-green-600 text-white">
                                            <Users class="size-4" />
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-medium">{{ c.name }}</span>
                                            <span class="block truncate text-[11px] text-muted-foreground">{{ c.title }}</span>
                                        </span>
                                        <span class="shrink-0 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ formatIdr(c.rate) }}/jam</span>
                                        <CheckCircle2 v-if="form.consultant === c.key" class="size-4 shrink-0 text-emerald-500" />
                                    </button>
                                </li>
                                <li v-if="!filteredConsultants.length" class="px-3 py-6 text-center text-sm text-muted-foreground">Konsultan tidak ditemukan.</li>
                            </ul>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- 2) Kalender & jam -->
                <div class="grid overflow-hidden rounded-b-3xl lg:grid-cols-2">
                    <!-- Kalender -->
                    <div class="border-b border-border/60 p-6 lg:border-b-0 lg:border-r">
                        <p class="mb-3 text-sm font-semibold">2. Pilih tanggal & jam</p>
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold capitalize">{{ monthLabel }}</h3>
                            <div class="flex items-center gap-1">
                                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg border border-border transition-colors hover:bg-muted disabled:opacity-30" :disabled="isCurrentMonth" @click="prevMonth"><ChevronLeft class="size-4" /></button>
                                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg border border-border hover:bg-muted" @click="nextMonth"><ChevronRight class="size-4" /></button>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-7 gap-1">
                            <div v-for="d in WEEKDAYS" :key="d" class="pb-2 text-center text-xs font-medium text-muted-foreground">{{ d }}</div>
                            <template v-for="(cell, i) in cells" :key="i">
                                <div v-if="!cell.date" class="aspect-square"></div>
                                <button
                                    v-else
                                    type="button"
                                    :disabled="!isSelectable(cell.date)"
                                    class="relative flex aspect-square items-center justify-center rounded-lg text-sm transition-colors"
                                    :class="[
                                        form.date === cell.date
                                            ? 'bg-emerald-500 font-semibold text-white'
                                            : isSelectable(cell.date)
                                              ? 'cursor-pointer border border-transparent hover:border-emerald-500 hover:bg-emerald-500/10'
                                              : 'cursor-not-allowed text-muted-foreground/40 line-through',
                                        isToday(cell.date) && form.date !== cell.date ? 'ring-1 ring-emerald-500' : '',
                                    ]"
                                    @click="selectDate(cell.date)"
                                >
                                    <span v-if="bookingCount(cell.date)" class="absolute -top-1 left-1/2 -translate-x-1/2 rounded-full px-1.5 py-px text-[9px] font-bold leading-none shadow-sm" :class="form.date === cell.date ? 'bg-white text-emerald-600' : 'bg-emerald-500 text-white'">
                                        {{ bookingCount(cell.date) }}
                                    </span>
                                    {{ cell.day }}
                                </button>
                            </template>
                        </div>

                        <div class="mt-5 flex items-start gap-2 rounded-lg bg-muted/40 p-3 text-xs text-muted-foreground">
                            <Info class="mt-0.5 size-3.5 shrink-0" />
                            <span>Senin–Kamis 09.00–21.00 (istirahat 12.00–13.00). Jumat 09.00–21.00 (istirahat 11.00–14.00). Sabtu & Minggu libur.</span>
                        </div>
                    </div>

                    <!-- Slot + Form -->
                    <div class="p-6">
                        <template v-if="!form.date">
                            <div class="flex h-full min-h-[280px] flex-col items-center justify-center text-center text-muted-foreground">
                                <CalendarCheck class="size-10 opacity-30" />
                                <p class="mt-3 text-sm">Pilih tanggal untuk melihat jam yang tersedia.</p>
                            </div>
                        </template>

                        <template v-else>
                            <div class="flex items-center gap-2 text-sm font-medium">
                                <Clock class="size-4 text-emerald-500" /> <span class="capitalize">{{ selectedLabel }}</span>
                            </div>

                            <p class="mt-4 text-xs font-medium uppercase tracking-wide text-muted-foreground">Pilih jam (boleh lebih dari satu)</p>
                            <div class="mt-2 grid grid-cols-3 gap-2 sm:grid-cols-4">
                                <button
                                    v-for="time in slots"
                                    :key="time"
                                    type="button"
                                    :disabled="slotDisabled(time)"
                                    class="relative rounded-lg border py-2 text-sm font-medium transition-colors"
                                    :class="form.times.includes(time)
                                        ? 'border-emerald-500 bg-emerald-500 text-white'
                                        : slotDisabled(time)
                                          ? 'cursor-not-allowed border-border/50 text-muted-foreground/40 line-through'
                                          : 'border-border hover:border-emerald-500 hover:bg-emerald-500/10'"
                                    @click="toggleTime(time)"
                                >
                                    <CheckCircle2 v-if="form.times.includes(time)" class="absolute -right-1.5 -top-1.5 size-4 rounded-full bg-white text-emerald-600" />
                                    {{ time }}
                                </button>
                            </div>
                            <p v-if="form.errors.times" class="mt-1 text-xs text-rose-500">{{ form.errors.times }}</p>

                            <!-- Form + ringkasan harga -->
                            <div v-if="form.times.length" class="mt-6 space-y-3 border-t border-border/60 pt-5">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium">Nama <span class="text-rose-500">*</span></label>
                                        <input v-model="form.name" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                                        <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium">Email <span class="text-rose-500">*</span></label>
                                        <input v-model="form.email" type="email" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                                        <p v-if="form.errors.email" class="mt-1 text-xs text-rose-500">{{ form.errors.email }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">No. WhatsApp</label>
                                    <input v-model="form.phone" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Catatan</label>
                                    <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Hal yang ingin dikonsultasikan..."></textarea>
                                </div>

                                <!-- Ringkasan harga -->
                                <div class="rounded-lg bg-emerald-500/5 p-4 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-muted-foreground">Konsultan</span>
                                        <span class="font-medium">{{ selectedConsultant?.name }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="text-muted-foreground">Jam</span>
                                        <span class="font-medium">{{ form.times.join(', ') }} <span class="text-muted-foreground">({{ form.times.length }} jam)</span></span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="text-muted-foreground">Tarif</span>
                                        <span class="font-medium">{{ formatIdr(selectedConsultant?.rate) }} × {{ form.times.length }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between border-t border-emerald-500/20 pt-2 text-base">
                                        <span class="font-semibold">Total</span>
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatIdr(totalPrice) }}</span>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    :disabled="form.processing"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-emerald-400 to-green-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition-transform hover:-translate-y-0.5 disabled:opacity-60"
                                    @click="submit"
                                >
                                    <CalendarCheck class="size-4" /> {{ form.processing ? 'Memproses...' : 'Konfirmasi Jadwal' }}
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
