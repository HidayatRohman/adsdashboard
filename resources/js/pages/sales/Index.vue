<template>
  <Head title="Transaksi" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="editDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit Transaksi</DialogTitle>
        </DialogHeader>

        <div class="grid gap-3">
          <div>
            <label class="block text-sm mb-1">Tanggal Lead Masuk</label>
            <input type="date" v-model="form.tanggal_lead_masuk" class="w-full border p-2 rounded" required />
          </div>

          <div>
            <label class="block text-sm mb-1">Nominal Masuk</label>
            <input type="number" v-model.number="form.nominal_masuk" class="w-full border p-2 rounded" required min="0" />
          </div>
        </div>

        <DialogFooter class="mt-4 flex gap-2">
          <DialogClose as-child>
            <button class="btn">Batal</button>
          </DialogClose>
          <button class="btn btn-primary" @click.prevent="handleSubmit">Update</button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Input Transaksi</h2>

          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:items-end">
            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Tanggal Transfer</label>
              <input type="date" v-model="form.tanggal_transfer" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Tanggal Lead Masuk</label>
              <input type="date" v-model="form.tanggal_lead_masuk" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Periode Lead (bulan)</label>
              <select v-model.number="form.periode_lead_bulan" class="w-full border p-2 rounded">
                <option value="">--Pilih Bulan--</option>
                <option v-for="(m, idx) in months" :key="idx" :value="idx+1">{{ m }}</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Nama Lengkap Mitra</label>
              <input v-model="form.nama_lengkap_mitra" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">No WA</label>
              <input v-model="form.no_wa" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Umur</label>
              <input type="number" v-model.number="form.umur" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Pekerjaan</label>
              <select v-model="form.pekerjaan" class="w-full border p-2 rounded">
                <option value="">--Pilih--</option>
                <option>Pelajar</option>
                <option>Mahasiswa</option>
                <option>Karyawan Swasta</option>
                <option>Pegawai Negeri</option>
                <option>Wiraswasta</option>
                <option>Freelancer</option>
                <option>Lainnya</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Nama Paket (produk)</label>
              <select v-model="form.product_id" class="w-full border p-2 rounded">
                <option value="">--Pilih Paket--</option>
                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} - Rp {{ formatNumber(p.price) }}</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Lead Awal</label>
              <select v-model="form.lead_awal" class="w-full border p-2 rounded">
                <option value="">--Pilih--</option>
                <option>IG</option>
                <option>FB</option>
                <option>TT</option>
                <option>WEB</option>
                <option>ORGANIK</option>
                <option>UNKNOWN</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Sumber</label>
              <select v-model="form.sumber" class="w-full border p-2 rounded">
                <option value="">--Pilih--</option>
                <option>IG</option>
                <option>FB</option>
                <option>TT</option>
                <option>WEB</option>
                <option>ORGANIK</option>
                <option>UNKNOWN</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Provinsi</label>
              <select v-model="form.provinsi" class="w-full border p-2 rounded">
                <option value="">--Pilih Provinsi--</option>
                <option v-for="prov in provinces" :key="prov">{{ prov }}</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Kabupaten</label>
              <select v-model="form.kabupaten" class="w-full border p-2 rounded">
                <option value="">--Pilih Kabupaten--</option>
                <option v-for="kab in kabupatenList" :key="kab">{{ kab }}</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Status</label>
              <select v-model="form.status" class="w-full border p-2 rounded">
                <option value="">--Pilih--</option>
                <option>DP</option>
                <option>Tambahan DP</option>
                <option>Tanda Jadi</option>
                <option>Pelunasan</option>
              </select>
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Nominal Masuk (Rp)</label>
              <input type="number" v-model.number="form.nominal_masuk" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Harga Paket (Rp)</label>
              <input type="number" v-model.number="form.harga_paket" class="w-full border p-2 rounded" />
            </div>

            <div class="sm:col-span-3">
              <button type="submit" class="btn btn-primary w-full">{{ editing ? 'Update' : 'Simpan' }}</button>
            </div>
          </form>
        </div>

        <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Daftar Transaksi</h2>

          <div v-if="!sales.data.length" class="text-sm text-muted-foreground">Belum ada data.</div>

          <table v-else class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">Tanggal</th>
                <th class="text-left p-2">Marketing</th>
                <th class="text-left p-2">Nama Mitra</th>
                <th class="text-left p-2">Produk</th>
                <th class="text-right p-2">Nominal Masuk</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="s in sales.data" :key="s.id" class="border-t">
                <td class="p-2">{{ formatDate(s.tanggal_lead_masuk) }}</td>
                <td class="p-2">{{ s.marketing?.name }}</td>
                <td class="p-2">{{ s.nama_lengkap_mitra }}</td>
                <td class="p-2">{{ s.product?.name }}</td>
                <td class="p-2 text-right">Rp{{ formatNumber(s.nominal_masuk) }}</td>
                <td class="p-2 text-center">
                  <button @click="edit(s)" class="mr-2 text-blue-600">Edit</button>
                  <button @click="destroy(s.id)" class="text-red-600">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="sales.prev_page_url" @click="goto(sales.current_page - 1)" class="btn">Previous</button>
            <button v-if="sales.next_page_url" @click="goto(sales.current_page + 1)" class="btn">Next</button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue';
import regions from '@/data/indonesia-regions.json';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogClose,
} from '@/components/ui/dialog';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Transaksi', href: '/sales' }];

const page = usePage();

type Product = { id: number; name: string; price: number };
type SaleItem = any;
type PaginatedSales = { data: SaleItem[]; current_page: number; next_page_url: string | null; prev_page_url: string | null };

const propsAny: any = (page.props as any);
const sales = computed<PaginatedSales>(() => (propsAny.sales as PaginatedSales) ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null });
const products = computed<Product[]>(() => (propsAny.products as Product[]) ?? []);

const editing = ref(false);
const editId = ref<number | null>(null);
const editDialogOpen = ref(false);
const form = reactive({
  tanggal_transfer: '',
  tanggal_lead_masuk: '',
  periode_lead_bulan: null as number | null,
  nama_lengkap_mitra: '',
  no_wa: '',
  umur: null as number | null,
  pekerjaan: '',
  product_id: null as number | null,
  lead_awal: '',
  sumber: '',
  kabupaten: '',
  provinsi: '',
  status: '',
  nominal_masuk: null as number | null,
  harga_paket: null as number | null,
});

const provinces = [
  'Aceh','Bali','Banten','Bengkulu','DI Yogyakarta','DKI Jakarta','Gorontalo','Jambi','Jawa Barat','Jawa Tengah','Jawa Timur','Kalimantan Barat','Kalimantan Selatan','Kalimantan Tengah','Kalimantan Timur','Kalimantan Utara','Kepulauan Bangka Belitung','Kepulauan Riau','Lampung','Maluku','Maluku Utara','Nusa Tenggara Barat','Nusa Tenggara Timur','Papua','Papua Barat','Riau','Sulawesi Barat','Sulawesi Selatan','Sulawesi Tengah','Sulawesi Tenggara','Sulawesi Utara','Sumatera Barat','Sumatera Selatan','Sumatera Utara'
];

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

// Minimal mapping provinsi -> kabupaten/kota (representatif). Extend as needed.
const provinceToKabupaten: Record<string, string[]> = (regions as Record<string, string[]> ) || {};

const kabupatenList = ref<string[]>([]);

watch(() => form.provinsi, (prov) => {
  if (!prov) {
    kabupatenList.value = [];
    return;
  }
  if (provinceToKabupaten[prov]) {
    kabupatenList.value = provinceToKabupaten[prov];
    return;
  }
  const foundKey = Object.keys(provinceToKabupaten).find(k => k.toLowerCase() === String(prov).toLowerCase());
  kabupatenList.value = foundKey ? provinceToKabupaten[foundKey] : [];
});

// Auto-fill harga_paket when a product is selected
watch(() => form.product_id, (pid) => {
  if (!pid) {
    form.harga_paket = null;
    return;
  }
  const found = products.value.find(p => p.id === pid as number);
  if (found) {
    form.harga_paket = found.price;
  }
});

function goto(pageNum: number) {
  router.get(route('sales.index', { page: pageNum }));
}

function handleSubmit() {
  const payload = { ...form };
  const finish = () => {
    clearForm();
    // reload current page to show newly created/updated record
    router.get(route('sales.index'));
  };

  if (editing.value && editId.value) {
    router.patch(route('sales.update', editId.value), payload, { onSuccess: finish });
  } else {
    router.post(route('sales.store'), payload, { onSuccess: finish });
  }
}

function edit(s: any) {
  editing.value = true;
  editId.value = s.id;
  form.tanggal_transfer = toInputDate(s.tanggal_transfer);
  form.tanggal_lead_masuk = toInputDate(s.tanggal_lead_masuk);
  form.periode_lead_bulan = s.periode_lead_bulan;
  form.nama_lengkap_mitra = s.nama_lengkap_mitra;
  form.no_wa = s.no_wa;
  form.umur = s.umur;
  form.pekerjaan = s.pekerjaan;
  form.product_id = s.product_id;
  form.lead_awal = s.lead_awal;
  form.sumber = s.sumber;
  form.kabupaten = s.kabupaten;
  form.provinsi = s.provinsi;
  form.status = s.status;
  form.nominal_masuk = s.nominal_masuk;
  form.harga_paket = s.harga_paket;
  editDialogOpen.value = true;
}

function destroy(id: number) {
  if (!confirm('Hapus transaksi ini?')) return;
  router.delete(route('sales.destroy', id));
}

function clearForm() {
  editing.value = false;
  editId.value = null;
  form.tanggal_transfer = '';
  form.tanggal_lead_masuk = '';
  form.periode_lead_bulan = null;
  form.nama_lengkap_mitra = '';
  form.no_wa = '';
  form.umur = null;
  form.pekerjaan = '';
  form.product_id = null;
  form.lead_awal = '';
  form.sumber = '';
  form.kabupaten = '';
  form.provinsi = '';
  form.status = '';
  form.nominal_masuk = null;
  form.harga_paket = null;
  editDialogOpen.value = false;
}

function toInputDate(d: any) {
  if (!d) return '';
  const date = new Date(d);
  if (isNaN(date.getTime())) return d;
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

function formatNumber(n: number) {
  return n?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') || '0';
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
.btn { padding: 8px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff; cursor: pointer }
.btn-primary { background: #2563eb; color: #fff; border-color: transparent }
</style>
