<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import GoogleButton from '@/components/GoogleButton.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Buat akun baru',
        description: 'Daftar untuk melihat angka performa PnL secara penuh.',
    },
});
</script>

<template>
    <Head title="Daftar" />

    <GoogleButton label="Daftar dengan Google" />

    <div class="my-5 flex items-center gap-3 text-xs uppercase tracking-wide text-muted-foreground">
        <span class="h-px flex-1 bg-border"></span> atau lewat email <span class="h-px flex-1 bg-border"></span>
    </div>

    <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']" v-slot="{ errors, processing }" class="flex flex-col gap-5">
        <div class="grid gap-2">
            <Label for="name">Nama</Label>
            <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Nama lengkap" />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email" placeholder="nama@email.com" />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="password">Password</Label>
            <PasswordInput id="password" required :tabindex="3" autocomplete="new-password" name="password" placeholder="••••••••" :passwordrules="passwordRules" />
            <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation">Konfirmasi password</Label>
            <PasswordInput id="password_confirmation" required :tabindex="4" autocomplete="new-password" name="password_confirmation" placeholder="••••••••" :passwordrules="passwordRules" />
            <InputError :message="errors.password_confirmation" />
        </div>

        <Button type="submit" class="mt-1 w-full bg-gradient-to-br from-emerald-500 to-green-600 hover:opacity-90" tabindex="5" :disabled="processing" data-test="register-user-button">
            <Spinner v-if="processing" />
            Buat akun
        </Button>
    </Form>

    <div class="mt-6 text-center text-sm text-muted-foreground">
        Sudah punya akun?
        <TextLink :href="login()" :tabindex="6">Masuk di sini</TextLink>
    </div>
</template>
