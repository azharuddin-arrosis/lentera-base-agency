<script setup lang="ts">
withDefaults(defineProps<{ size?: number }>(), { size: 36 });
</script>

<template>
    <span
        class="lentera-badge inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-green-600 text-white shadow-sm"
        :style="{ width: `${size}px`, height: `${size}px` }"
    >
        <svg class="lentera-svg" viewBox="0 0 24 24" :width="size * 0.62" :height="size * 0.62" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <radialGradient id="lentera-glow" cx="50%" cy="55%" r="50%">
                    <stop offset="0%" stop-color="#fff" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="#fff" stop-opacity="0" />
                </radialGradient>
            </defs>

            <!-- glow -->
            <circle class="ll-glow" cx="12" cy="14" r="6.5" fill="url(#lentera-glow)" />

            <!-- api utama -->
            <path
                class="ll-flame"
                d="M8.5 14.5A2.5 2.5 0 0 0 11 12c1.5-1.26 2-5 2-5s.5 3.74 2 5a2.5 2.5 0 0 0 2.5 2.5c0 3.038-2.46 5.5-5.5 5.5S6.5 17.538 6.5 14.5a4.5 4.5 0 0 0 2 0Z"
                fill="#fff"
            />
            <!-- inti api -->
            <path
                class="ll-core"
                d="M10.6 15.2a1.4 1.4 0 0 0 1.4-1.4c.8-.7 1.1-2.6 1.1-2.6s.3 1.9 1.1 2.6a1.4 1.4 0 0 0 1.4 1.4c0 1.6-1.3 2.9-2.5 2.9s-2.5-1.3-2.5-2.9Z"
                fill="#bbf7d0"
            />

            <!-- percikan -->
            <circle class="ll-spark ll-s1" cx="9.5" cy="6.5" r="0.6" fill="#fff" />
            <circle class="ll-spark ll-s2" cx="15" cy="5.5" r="0.5" fill="#fff" />
            <circle class="ll-spark ll-s3" cx="12" cy="4" r="0.45" fill="#fff" />
        </svg>
    </span>
</template>

<style scoped>
.ll-flame {
    transform-box: fill-box;
    transform-origin: bottom center;
    animation: ll-flicker 1.5s ease-in-out infinite;
}
.ll-core {
    transform-box: fill-box;
    transform-origin: bottom center;
    animation: ll-flicker 0.9s ease-in-out infinite;
}
.ll-glow {
    transform-box: fill-box;
    transform-origin: center;
    animation: ll-pulse 2.4s ease-in-out infinite;
}
.ll-spark {
    transform-box: fill-box;
    animation: ll-spark 2.2s ease-out infinite;
    opacity: 0;
}
.ll-s2 {
    animation-delay: 0.8s;
}
.ll-s3 {
    animation-delay: 1.5s;
}

@keyframes ll-flicker {
    0%,
    100% {
        transform: scaleY(1) scaleX(1) rotate(0deg);
        opacity: 1;
    }
    25% {
        transform: scaleY(1.09) scaleX(0.96) rotate(-1.5deg);
        opacity: 0.92;
    }
    50% {
        transform: scaleY(0.93) scaleX(1.04) rotate(0deg);
        opacity: 1;
    }
    75% {
        transform: scaleY(1.05) scaleX(0.98) rotate(1.5deg);
        opacity: 0.95;
    }
}
@keyframes ll-pulse {
    0%,
    100% {
        opacity: 0.35;
        transform: scale(1);
    }
    50% {
        opacity: 0.75;
        transform: scale(1.18);
    }
}
@keyframes ll-spark {
    0% {
        opacity: 0;
        transform: translateY(3px) scale(0.6);
    }
    30% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translateY(-7px) scale(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .ll-flame,
    .ll-core,
    .ll-glow,
    .ll-spark {
        animation: none;
    }
    .ll-spark {
        opacity: 0;
    }
}
</style>
