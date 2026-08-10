<script setup lang="ts">
import { Check, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';

withDefaults(defineProps<{ label?: string }>(), { label: 'Hapus item ini?' });
const emit = defineEmits<{ confirm: [] }>();

const open = ref(false);
</script>

<template>
    <div class="inline-flex items-center gap-1">
        <template v-if="!open">
            <button
                type="button"
                class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400"
                :title="label"
                @click="open = true"
            >
                <Trash2 class="size-4" />
            </button>
        </template>
        <template v-else>
            <span class="text-xs text-muted-foreground">Yakin?</span>
            <button
                type="button"
                class="inline-flex size-8 items-center justify-center rounded-lg bg-rose-500 text-white hover:bg-rose-600"
                title="Ya, hapus"
                @click="emit('confirm'); open = false"
            >
                <Check class="size-4" />
            </button>
            <button
                type="button"
                class="inline-flex size-8 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-muted"
                title="Batal"
                @click="open = false"
            >
                <X class="size-4" />
            </button>
        </template>
    </div>
</template>
