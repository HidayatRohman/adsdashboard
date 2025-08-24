<template>
  <Head title="Products" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="dialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editing ? 'Edit Produk' : 'Tambah Produk' }}</DialogTitle>
        </DialogHeader>


        <form @submit.prevent="handleSubmit" class="grid gap-3">
          <div>
            <label class="block text-sm mb-1">Brand</label>
            <select v-model="form.brand_id" class="w-full border p-2 rounded">
              <option value="">--Pilih Brand--</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm mb-1">Nama Produk</label>
            <input v-model="form.name" class="w-full border p-2 rounded" required />
          </div>

          <div>
            <label class="block text-sm mb-1">Harga Default (Rp)</label>
            <input type="number" v-model.number="form.price" class="w-full border p-2 rounded" min="0" />
          </div>

          <div>
            <label class="block text-sm mb-1">Variasi Harga</label>
            <div class="space-y-2">
              <div v-for="(v, idx) in form.variations" :key="idx" class="flex gap-2 items-center">
                    <input v-model="v.name" placeholder="Nama variasi" class="flex-1 border p-2 rounded" />
                    <input type="number" v-model.number="v.price" placeholder="Harga" class="w-36 border p-2 rounded" />
                    <button type="button" class="pill-btn small" @click.prevent="form.variations.splice(idx,1)">
                      <Trash2 class="h-4 w-4" />
                    </button>
              </div>
              <button type="button" class="pill-btn" @click.prevent="form.variations.push({ name: '', price: null })">Tambah Variasi</button>
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
            <h2 class="text-lg font-semibold mb-3">Produk</h2>
            <button class="pill-btn" @click.prevent="openCreate">Tambah Produk</button>
          </div>

          <div class="mt-2">
            <input v-model="filters.search" @keyup.enter="reload" placeholder="Cari produk..." class="w-full border p-2 rounded" />
          </div>
        </div>

        <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Daftar Produk</h2>

          <div v-if="!products.data.length" class="text-sm text-muted-foreground">Belum ada data.</div>

          <table v-else class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">Nama</th>
                <th class="text-left p-2">Brand</th>
                <th class="text-right p-2">Harga</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in products.data" :key="p.id" class="border-t">
                <td class="p-2">{{ p.name }}</td>
                <td class="p-2">{{ p.brand?.name }}</td>
                <td class="p-2 text-right">Rp{{ formatNumber(p.price) }}</td>
                <td class="p-2 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="edit(p)" class="pill-btn small" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M3 21v-4a4 4 0 014-4h6"/></svg>
                    </button>

                    <button @click="destroy(p.id)" class="pill-btn small pill-danger" title="Hapus">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 6a1 1 0 10-2 0v6a1 1 0 102 0V8zm6 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="products.prev_page_url" @click="goto(products.current_page - 1)" class="btn">Previous</button>
            <button v-if="products.next_page_url" @click="goto(products.current_page + 1)" class="btn">Next</button>
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
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog';
import { Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Produk', href: '/products' }];

const page = usePage();
type Product = { id: number; name: string; price: number; brand?: { id: number; name: string }; variations?: Array<{ id?: number; name?: string; price?: number }> };
type PaginatedProducts = { data: Product[]; current_page: number; next_page_url: string | null; prev_page_url: string | null };
const propsAny: any = (page.props as any);
const products = computed<PaginatedProducts>(() => (propsAny.products as PaginatedProducts) ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null });
const brands = computed(() => (propsAny.brands as Array<{ id: number; name: string }>) ?? []);

const dialogOpen = ref(false);
const editing = ref(false);
const editId = ref<number | null>(null);
const form = reactive({ name: '', price: null as number | null, brand_id: null as number | null, variations: [] as Array<{ id?: number | null; name?: string | null; price?: number | null }> });
const filters = reactive({ search: '' });

function formatNumber(n: number) {
  return n?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') || '0';
}

function goto(pageNum: number) {
  router.get(route('products.index', { page: pageNum }));
}

function reload() {
  router.get(route('products.index', { search: filters.search }));
}

function handleSubmit() {
  const payload = { ...form };
  const finish = () => {
    clearForm();
    router.get(route('products.index'));
  };
  if (editing.value && editId.value) {
    router.patch(route('products.update', editId.value), payload, { onSuccess: finish });
  } else {
    router.post(route('products.store'), payload, { onSuccess: finish });
  }
}

function openCreate() {
  clearForm();
  editing.value = false;
  dialogOpen.value = true;
}

function edit(p: any) {
  editing.value = true;
  editId.value = p.id;
  form.name = p.name;
  form.price = p.price;
  form.brand_id = p.brand?.id ?? null;
  form.variations = (p.variations ?? []).map((v: any) => ({ id: v.id, name: v.name, price: v.price }));
  dialogOpen.value = true;
}

function destroy(id: number) {
  if (!confirm('Hapus produk ini?')) return;
  router.delete(route('products.destroy', id), { onSuccess: () => router.get(route('products.index')) });
}

function clearForm() {
  editing.value = false;
  editId.value = null;
  form.name = '';
  form.price = null;
  form.brand_id = null;
  form.variations = [];
  dialogOpen.value = false;
}
</script>

<style scoped>
.btn { padding: 8px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff; cursor: pointer }
.btn-primary { background: #2563eb; color: #fff; border-color: transparent }
  .pill-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .9rem; border-radius: 9999px; border: none; background: #4f46e5; color: #fff; cursor: pointer; box-shadow: 0 6px 20px rgba(79,70,229,0.18); transition: transform .08s ease, box-shadow .12s ease }
  .pill-btn.small { padding: .35rem .6rem; font-size: .85rem }
  .pill-btn:hover { transform: translateY(-2px) }
  .pill-btn.pill-danger { background: #dc2626 }
</style>
