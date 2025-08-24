<template>
  <Head title="Leads" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="dialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editing ? 'Edit Lead' : 'Tambah Lead' }}</DialogTitle>
        </DialogHeader>

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
          </div>

          <DialogFooter class="mt-4 flex gap-2">
            <DialogClose as-child>
              <button type="button" class="pill-btn" @click.prevent="clearForm">Batal</button>
            </DialogClose>
            <button type="submit" class="pill-btn" style="background:#4f46e5">{{ editing ? 'Update' : 'Simpan' }}</button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold mb-3">Leads</h2>
            <button class="pill-btn" @click.prevent="openCreate">Tambah Lead</button>
          </div>
        </div>

        <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Daftar Leads</h2>

          <div v-if="!leads.data.length" class="text-sm text-muted-foreground">Belum ada data.</div>

          <table v-else class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">Tanggal</th>
                <th class="text-left p-2">Marketing</th>
                <th class="text-left p-2">Nama Customer</th>
                <th class="text-left p-2">Produk</th>
                <th class="text-left p-2">Respon Terakhir</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="l in leads.data" :key="l.id" class="border-t">
                <td class="p-2">{{ formatDate(l.created_at) }}</td>
                <td class="p-2">{{ l.marketing?.name }}</td>
                <td class="p-2">{{ l.customer_name }}</td>
                <td class="p-2">{{ l.product?.name }}</td>
                <td class="p-2">{{ l.last_response }}</td>
                <td class="p-2 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="edit(l)" class="pill-btn small" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M3 21v-4a4 4 0 014-4h6"/></svg>
                    </button>

                    <button @click="destroy(l.id)" class="pill-btn small pill-danger" title="Hapus">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 6a1 1 0 10-2 0v6a1 1 0 102 0V8zm6 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="leadsAny.prev_page_url" @click="goto(leadsAny.current_page - 1)" class="pill-btn small">Previous</button>
            <button v-if="leadsAny.next_page_url" @click="goto(leadsAny.current_page + 1)" class="pill-btn small">Next</button>
          </div>
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
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog';

const page = usePage();
type LeadItem = any;
type PaginatedLeads = { data: LeadItem[]; current_page: number; next_page_url: string | null; prev_page_url: string | null };
const propsAny: any = (page.props as any);
const leads = computed<PaginatedLeads>(() => (propsAny.leads as PaginatedLeads) ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null });
const leadsAny: any = leads;

const dialogOpen = ref(false);
const editing = ref(false);
const editId = ref<number | null>(null);

const formState = reactive({ product_id: null as number | null, customer_name: '', customer_phone: '', address: '', last_response: '' });

// reuse earlier names for template binding
const form: any = formState;

function goto(pageNum: number) {
  router.get(route('leads.index', { page: pageNum }));
}

function handleSubmit() {
  const payload = { ...form };
  const finish = () => {
    clearForm();
    router.get(route('leads.index'));
  };

  if (editing.value && editId.value) {
    router.patch(route('leads.update', editId.value), payload, { onSuccess: finish });
  } else {
    router.post(route('leads.store'), payload, { onSuccess: finish });
  }
}

function openCreate() {
  clearForm();
  editing.value = false;
  dialogOpen.value = true;
}

function edit(l: any) {
  editing.value = true;
  editId.value = l.id;
  form.product_id = l.product_id ?? null;
  form.customer_name = l.customer_name ?? '';
  form.customer_phone = l.customer_phone ?? '';
  form.address = l.address ?? '';
  form.last_response = l.last_response ?? '';
  dialogOpen.value = true;
}

function destroy(id: number) {
  if (!confirm('Hapus lead ini?')) return;
  router.delete(route('leads.destroy', id), { onSuccess: () => router.get(route('leads.index')) });
}

function clearForm() {
  editing.value = false;
  editId.value = null;
  form.product_id = null;
  form.customer_name = '';
  form.customer_phone = '';
  form.address = '';
  form.last_response = '';
  dialogOpen.value = false;
}

function formatDate(d: string) {
  try {
    return new Date(d).toLocaleDateString();
  } catch (e) {
    return d;
  }
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
