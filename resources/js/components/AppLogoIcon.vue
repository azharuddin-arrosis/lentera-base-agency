<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        :class="className"
        v-bind="$attrs"
    >
        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c1.5-1.26 2-5 2-5s.5 3.74 2 5a2.5 2.5 0 0 0 2.5 2.5c0 3.038-2.46 5.5-5.5 5.5S6.5 17.538 6.5 14.5a4.5 4.5 0 0 0 2 0Z" />
    </svg>
</template>
