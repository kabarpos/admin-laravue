<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { Mail } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import axios from 'axios';

const props = defineProps<{
  emailSettings: {
    id: number;
    mail_driver: string;
    mail_host: string | null;
    mail_port: string | null;
    mail_username: string | null;
    mail_password: string | null;
    mail_encryption: string | null;
    mail_from_address: string | null;
    mail_from_name: string | null;
    enable_verification: boolean;
    verification_template: string | null;
  }
}>();

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pengaturan Email',
    href: route('admin.email.index'),
  },
];

const form = useForm({
  mail_driver: props.emailSettings?.mail_driver || 'smtp',
  mail_host: props.emailSettings?.mail_host || '',
  mail_port: props.emailSettings?.mail_port || '',
  mail_username: props.emailSettings?.mail_username || '',
  mail_password: props.emailSettings?.mail_password || '',
  mail_encryption: props.emailSettings?.mail_encryption || 'tls',
  mail_from_address: props.emailSettings?.mail_from_address || '',
  mail_from_name: props.emailSettings?.mail_from_name || '',
  enable_verification: props.emailSettings?.enable_verification ?? true,
  verification_template: props.emailSettings?.verification_template || `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email Anda</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #4f46e5; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; }
        .footer { background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email Anda</h1>
        </div>
        <div class="content">
            <p>Hai {name},</p>
            <p>Terima kasih telah mendaftar di aplikasi kami. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.</p>
            <div style="text-align: center;">
                <a href="{verification_url}" class="button">Verifikasi Email</a>
            </div>
            <p>Jika Anda tidak membuat akun, abaikan email ini.</p>
            <p>Salam,<br>Tim Kami</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>`,
});

const processing = ref(false);
const showTestEmailDialog = ref(false);
const testEmail = ref('');

const updateEmailSettings = () => {
  processing.value = true;
  form.put(route('admin.email.update'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan email berhasil disimpan',
      });
      processing.value = false;
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan',
      });
      processing.value = false;
    },
  });
};

const sendTestEmail = () => {
  processing.value = true;
  axios.post(route('admin.email.test'), {
    test_email: testEmail.value,
  }).then(() => {
    toast.success('Berhasil', {
      description: 'Email tes berhasil dikirim',
    });
    showTestEmailDialog.value = false;
  }).catch((error: any) => {
    toast.error('Gagal', {
      description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
    });
  }).finally(() => {
    processing.value = false;
  });
};
</script>

<template>
  <Head title="Pengaturan Email" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Pengaturan Email</h1>
      </div>

      <div class="bg-card text-card-foreground rounded-xl shadow border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <div class="p-6">
          <h2 class="text-lg font-medium">Konfigurasi SMTP</h2>
          <p class="text-muted-foreground mt-1">Atur konfigurasi email server untuk notifikasi dan verifikasi.</p>
        </div>
        
        <div class="p-6 border-t">
          <form @submit.prevent="updateEmailSettings">
            <!-- Pengaturan Driver -->
            <div class="mb-6">
              <Label for="mail_driver">Driver Email</Label>
              <select v-model="form.mail_driver" id="mail_driver" class="flex h-10 mt-1.5 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <option value="smtp">SMTP</option>
                <option value="mailgun">Mailgun</option>
                <option value="ses">Amazon SES</option>
                <option value="log">Log (untuk pengujian)</option>
              </select>
            </div>
            
            <!-- SMTP Settings -->
            <div v-if="form.mail_driver === 'smtp'" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="mail_host">SMTP Host</Label>
                  <Input v-model="form.mail_host" id="mail_host" type="text" class="mt-1.5" />
                </div>
                <div>
                  <Label for="mail_port">SMTP Port</Label>
                  <Input v-model="form.mail_port" id="mail_port" type="text" class="mt-1.5" />
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="mail_username">SMTP Username</Label>
                  <Input v-model="form.mail_username" id="mail_username" type="text" class="mt-1.5" />
                </div>
                <div>
                  <Label for="mail_password">SMTP Password</Label>
                  <Input v-model="form.mail_password" id="mail_password" type="password" class="mt-1.5" />
                </div>
              </div>
              
              <div>
                <Label for="mail_encryption">Enkripsi</Label>
                <select v-model="form.mail_encryption" id="mail_encryption" class="flex h-10 mt-1.5 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                  <option value="">Tidak Ada</option>
                </select>
              </div>
            </div>
            
            <!-- From Address -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
              <div>
                <Label for="mail_from_address">Alamat Email Pengirim</Label>
                <Input v-model="form.mail_from_address" id="mail_from_address" type="email" class="mt-1.5" />
              </div>
              <div>
                <Label for="mail_from_name">Nama Pengirim</Label>
                <Input v-model="form.mail_from_name" id="mail_from_name" type="text" class="mt-1.5" />
              </div>
            </div>
            
            <!-- Verifikasi Email -->
            <div class="mt-8">
              <h3 class="text-md font-medium">Pengaturan Verifikasi Email</h3>
              <div class="mt-4">
                <div class="flex items-center space-x-2">
                  <label 
                    class="inline-flex items-center gap-2 cursor-pointer"
                    for="enable_verification"
                  >
                    <Switch 
                      id="enable_verification"
                      v-model="form.enable_verification"
                      name="enable_verification"
                    />
                    <span>Aktifkan verifikasi email untuk pendaftaran pengguna baru</span>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Template Email -->
            <div class="mt-4" v-if="form.enable_verification">
              <Label for="verification_template">Template Email Verifikasi (HTML)</Label>
              <textarea 
                v-model="form.verification_template" 
                id="verification_template" 
                rows="10" 
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none mt-1.5 font-mono text-sm"
                placeholder="Template HTML dengan variabel: {name}, {verification_url}"
              ></textarea>
              <p class="text-xs text-muted-foreground mt-1">
                Gunakan variabel: {name} untuk nama pengguna, {verification_url} untuk tautan verifikasi
              </p>
            </div>
            
            <div class="flex gap-4 mt-8">
              <Button type="submit" :disabled="processing">Simpan Pengaturan</Button>
              <Button type="button" variant="outline" @click="showTestEmailDialog = true">Kirim Email Tes</Button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Dialog Test Email -->
    <ConfirmationDialog
      :open="showTestEmailDialog"
      @update:open="showTestEmailDialog = $event"
      title="Kirim Email Tes"
      :icon="Mail"
      confirmLabel="Kirim Email"
      :loading="processing"
      @confirm="sendTestEmail()"
    >
      <div class="py-2">
        <Label for="test_email">Alamat Email Penerima</Label>
        <Input 
          v-model="testEmail" 
          id="test_email" 
          type="email" 
          placeholder="Masukkan alamat email untuk pengujian"
          class="mt-1.5" 
        />
      </div>
      <p class="text-sm text-muted-foreground mt-2">
        Email tes akan dikirim untuk memverifikasi pengaturan SMTP Anda.
      </p>
    </ConfirmationDialog>
  </AppLayout>
</template> 