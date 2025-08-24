<template>
  <Head title="Transaksi" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="editDialogOpen">
      <DialogContent class="dialog-custom">
        <DialogHeader>
          <DialogTitle>{{ editing ? 'Edit Transaksi' : 'Tambah Transaksi' }}</DialogTitle>
        </DialogHeader>

        <!-- Full form now lives inside the dialog for both create and edit -->
        <form @submit.prevent="handleSubmit" class="grid gap-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="form-label">Tanggal Transfer</label>
              <input type="date" v-model="form.tanggal_transfer" class="form-input" />
              <div class="helper-text">Tanggal transfer jika sudah dilakukan</div>
            </div>

            <div>
              <label class="form-label">Tanggal Lead Masuk</label>
              <input type="date" v-model="form.tanggal_lead_masuk" class="form-input" />
              <div class="helper-text">Tanggal lead pertama kali masuk</div>
            </div>

            <div>
              <label class="form-label">Periode Lead (bulan)</label>
              <select v-model.number="form.periode_lead_bulan" class="form-input">
                <option value="">--Pilih Bulan--</option>
                <option v-for="(m, idx) in months" :key="idx" :value="idx+1">{{ m }}</option>
              </select>
            </div>

            <div>
              <label class="form-label">Nama Lengkap Mitra</label>
              <input v-model="form.nama_lengkap_mitra" class="form-input" placeholder="Nama lengkap sesuai KTP" />
              <div class="helper-text">Contoh: Budi Santoso</div>
            </div>

            <div>
              <label class="form-label">No WA</label>
              <input v-model="form.no_wa" class="form-input" placeholder="0812xxxx" />
              <div class="helper-text">Gunakan nomor aktif untuk follow up</div>
            </div>

            <div>
              <label class="form-label">Umur</label>
              <input type="number" v-model.number="form.umur" class="form-input" placeholder="Umur" />
            </div>

            <div>
              <label class="form-label">Pekerjaan</label>
              <select v-model="form.pekerjaan" class="form-input">
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

            <div>
              <label class="form-label">Nama Paket (produk)</label>
              <select v-model="form.product_id" class="form-input">
                <option value="">--Pilih Paket--</option>
                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} - Rp {{ formatNumber(p.price) }}</option>
              </select>
            </div>

            <div>
              <label class="form-label">Lead Awal</label>
              <select v-model="form.lead_awal" class="form-input">
                <option value="">--Pilih--</option>
                <option>IG</option>
                <option>FB</option>
                <option>TT</option>
                <option>WEB</option>
                <option>ORGANIK</option>
                <option>UNKNOWN</option>
              </select>
            </div>

            <div>
              <label class="form-label">Sumber</label>
              <select v-model="form.sumber" class="form-input">
                <option value="">--Pilih--</option>
                <option>IG</option>
                <option>FB</option>
                <option>TT</option>
                <option>WEB</option>
                <option>ORGANIK</option>
                <option>UNKNOWN</option>
              </select>
            </div>

            <div>
              <label class="form-label">Provinsi</label>
              <select v-model="form.provinsi" class="form-input">
                <option value="">--Pilih Provinsi--</option>
                <option v-for="prov in provinces" :key="prov">{{ prov }}</option>
              </select>
            </div>

            <div>
              <label class="form-label">Kabupaten</label>
              <select v-model="form.kabupaten" class="form-input">
                <option value="">--Pilih Kabupaten--</option>
                <option v-for="kab in kabupatenList" :key="kab">{{ kab }}</option>
              </select>
            </div>

            <div>
              <label class="form-label">Status</label>
              <select v-model="form.status" class="form-input">
                <option value="">--Pilih--</option>
                <option>DP</option>
                <option>Tambahan DP</option>
                <option>Tanda Jadi</option>
                <option>Pelunasan</option>
              </select>
            </div>

            <div>
              <label class="form-label">Nominal Masuk (Rp)</label>
              <input type="number" v-model.number="form.nominal_masuk" class="form-input" placeholder="0" />
            </div>

            <div>
              <label class="form-label">Harga Paket (Rp)</label>
              <input type="number" v-model.number="form.harga_paket" class="form-input" placeholder="0" />
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

  <div class="flex h-full flex-1 flex-col gap-6 rounded-xl px-4 md:px-8 py-6 md:py-10 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
  <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 md:p-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold mb-3">Input Transaksi</h2>
            <button class="pill-btn" @click.prevent="openCreate">Tambah Transaksi</button>
          </div>
        </div>

  <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4 md:p-6">
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
                  <div class="flex items-center justify-center gap-2">
                    <button @click="edit(s)" class="pill-btn small" title="Edit">
                      <Edit2 class="h-4 w-4 text-white" />
                    </button>

                    <button @click="destroy(s.id)" class="pill-btn small pill-danger" title="Hapus">
                      <Trash2 class="h-4 w-4 text-white" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="salesAny.prev_page_url" @click="goto(salesAny.current_page - 1)" class="pill-btn small">Previous</button>
            <button v-if="salesAny.next_page_url" @click="goto(salesAny.current_page + 1)" class="pill-btn small">Next</button>
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
import { Edit2, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Transaksi', href: '/sales' }];

const page = usePage();

type Product = { id: number; name: string; price: number };
type SaleItem = any;
type PaginatedSales = { data: SaleItem[]; current_page: number; next_page_url: string | null; prev_page_url: string | null };

const propsAny: any = (page.props as any);
const sales = computed<PaginatedSales>(() => (propsAny.sales as PaginatedSales) ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null });
const salesAny: any = sales;
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

function openCreate() {
  clearForm();
  editing.value = false;
  editDialogOpen.value = true;
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

  .form-label { display:block; font-size: .875rem; margin-bottom: .35rem; color: #374151 }
  .form-input { width:100%; padding: .6rem .75rem; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; transition: border-color .12s ease, box-shadow .12s ease }
  .form-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 6px 18px rgba(99,102,241,0.12) }
  .helper-text { font-size: .8rem; color: #6b7280; margin-top: .25rem }

  .pill-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .45rem .8rem; border-radius: 9999px; border: none; background: #4f46e5; color: #fff; cursor: pointer; box-shadow: 0 6px 20px rgba(79,70,229,0.12); transition: transform .08s ease }
  .pill-btn.small { padding: .3rem .5rem; font-size: .82rem }
  .pill-btn:hover { transform: translateY(-2px) }
  .pill-btn.pill-danger { background: #ef4444 }

  @media (max-width:640px) {
    .form-input { font-size: .95rem }
  }
</style>

<style scoped>
  /* dialog overrides for this page */
  /* wider, viewport-based dialog to match provided screenshot */
  .dialog-custom { width: 70vw !important; max-width: 1200px !important; margin: 10vh auto !important; }
  @media (max-width: 1200px) { .dialog-custom { width: 90vw !important; max-width: 95% !important; margin: 6vh auto !important } }
</style>
