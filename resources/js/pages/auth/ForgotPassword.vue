<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Lupa password?',
        description: 'Masukkan email Anda untuk menerima tautan reset password.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Lupa password" />

    <div v-if="status" class="mb-4 rounded-lg bg-emerald-500/10 px-4 py-2 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400">
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" name="email" autocomplete="off" autofocus placeholder="nama@email.com" />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6">
                <Button class="w-full bg-gradient-to-br from-emerald-500 to-green-600 hover:opacity-90" :disabled="processing" data-test="email-password-reset-link-button">
                    <Spinner v-if="processing" />
                    Kirim tautan reset
                </Button>
            </div>
        </Form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Ingat password Anda?</span>
            <TextLink :href="login()">Kembali masuk</TextLink>
        </div>
    </div>
</template>
