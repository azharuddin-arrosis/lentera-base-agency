<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import GoogleButton from '@/components/GoogleButton.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Selamat datang kembali 👋',
        description: 'Masukkan email dan password untuk masuk ke dashboard.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Masuk" />

    <div v-if="status" class="mb-4 rounded-lg bg-emerald-500/10 px-4 py-2 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400">
        {{ status }}
    </div>

    <GoogleButton label="Masuk dengan Google" />

    <div class="my-5 flex items-center gap-3 text-xs uppercase tracking-wide text-muted-foreground">
        <span class="h-px flex-1 bg-border"></span> atau lewat email <span class="h-px flex-1 bg-border"></span>
    </div>

    <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }" class="flex flex-col gap-5">
        <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email" placeholder="nama@email.com" />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <div class="flex items-center justify-between">
                <Label for="password">Password</Label>
                <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5">Lupa password?</TextLink>
            </div>
            <PasswordInput id="password" name="password" required :tabindex="2" autocomplete="current-password" placeholder="••••••••" />
            <InputError :message="errors.password" />
        </div>

        <Label for="remember" class="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span class="text-sm">Ingat saya</span>
        </Label>

        <Button type="submit" class="w-full bg-gradient-to-br from-emerald-500 to-green-600 hover:opacity-90" :tabindex="4" :disabled="processing" data-test="login-button">
            <Spinner v-if="processing" />
            Masuk
        </Button>
    </Form>

    <div class="mt-6 text-center text-sm text-muted-foreground">
        Belum punya akun?
        <TextLink :href="register()" :tabindex="5">Daftar di sini</TextLink>
    </div>
</template>
