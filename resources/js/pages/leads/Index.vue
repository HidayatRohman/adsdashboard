<template>
  <Head title="Leads" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl px-4 md:px-8 py-6 md:py-10 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative overflow-visible rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 md:p-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold mb-3">Input Lead</h2>
          </div>

          <form @submit.prevent="handleSubmit" class="grid gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <label class="form-label">Nama Marketing</label>
                <input class="form-input" :value="marketingName" disabled />
                <div class="helper-text">Diambil dari akun yang login (role Marketing)</div>
              </div>

              <div>
                <label class="form-label">Nama Produk</label>
                <select v-model="form.product_id" class="form-input">
                  <option value="">--Pilih Produk--</option>
                  <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </div>

              <div>
                <label class="form-label">Nama Customer</label>
                <input v-model="form.customer_name" class="form-input" />
              </div>

              <div>
                <label class="form-label">No Hp Customer</label>
                <input v-model="form.customer_phone" class="form-input" />
              </div>

              <div class="md:col-span-2 lg:col-span-4">
                <label class="form-label">Alamat</label>
                <input v-model="form.address" class="form-input" />
              </div>

              <div>
                <label class="form-label">Respon Terakhir</label>
                <select v-model="form.last_response" class="form-input">
                  <option value="">--Pilih Respon--</option>
                  <option v-for="r in responses" :key="r" :value="r">{{ r }}</option>
                </select>
              </div>

              <div class="lg:col-span-4">
                <DialogFooter class="mt-4 flex gap-2 justify-end">
                  <button type="button" class="pill-btn" @click.prevent="clearForm">Batal</button>
                  <button type="submit" class="pill-btn" style="background:#4f46e5">Simpan</button>
                </DialogFooter>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Leads', href: '/leads' }];
const page = usePage();

const propsAny: any = (page.props as any);
const products = computed(() => (propsAny.products ?? []) as Array<{ id: number; name: string }> );

// Marketing name from logged in user
const marketingName = computed(() => (propsAny.auth?.user?.name) ?? 'Marketing');

const responses = [
  'Greeting','Sistem','Fasilitas Paket','Proposal','Harga Paket','Minat FU','Alamat Kantor','Ongkir','Pertimbangkan','Modal','Kredit','COD','Info Rekening','Cari Lokasi','Cari Karyawan','Info Outlet','Training','Repeat Order','HPP & BEP','Harga Jual','Refund','MOU','Kesesuaian Promo','Bahan Baku Saja','Testimoni','Tidak Jelas','Closing'
];

const form = reactive({ product_id: null as number | null, customer_name: '', customer_phone: '', address: '', last_response: '' });

function handleSubmit() {
  const payload = { ...form };
  router.post(route('leads.store'), payload, { onSuccess: () => { clearForm(); router.get(route('leads.index')); } });
}

function clearForm() {
  form.product_id = null;
  form.customer_name = '';
  form.customer_phone = '';
  form.address = '';
  form.last_response = '';
}
</script>

<style scoped>
.form-label { display:block; font-size: .875rem; margin-bottom: .35rem; color: #374151 }
.form-input { width:100%; padding: .6rem .75rem; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; transition: border-color .12s ease, box-shadow .12s ease }
.form-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 6px 18px rgba(99,102,241,0.12) }
.helper-text { font-size: .8rem; color: #6b7280; margin-top: .25rem }
.pill-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .45rem .8rem; border-radius: 9999px; border: none; background: #4f46e5; color: #fff; cursor: pointer; box-shadow: 0 6px 20px rgba(79,70,229,0.12); transition: transform .08s ease }
.pill-btn.small { padding: .3rem .5rem; font-size: .82rem }
.pill-btn:hover { transform: translateY(-2px) }
.pill-btn.pill-danger { background: #ef4444 }
</style>
