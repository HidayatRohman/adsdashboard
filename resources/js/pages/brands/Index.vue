<template>
  <Head title="Brands" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="dialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editing ? 'Edit Brand' : 'Tambah Brand' }}</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="handleSubmit" class="grid gap-3" enctype="multipart/form-data">
          <div>
            <label class="block text-sm mb-1">Logo</label>
            <input type="file" @change="onFileChange" accept="image/*" />
            <div v-if="preview" class="mt-2">
              <img :src="preview" class="h-20 object-contain" />
            </div>
          </div>

          <div>
            <label class="block text-sm mb-1">Nama Brand</label>
            <input v-model="form.name" class="w-full border p-2 rounded" required />
          </div>

          <DialogFooter class="mt-4 flex gap-2">
            <DialogClose as-child>
              <button type="button" class="btn" @click.prevent="clearForm">Batal</button>
            </DialogClose>
            <button type="submit" class="btn btn-primary">{{ editing ? 'Update' : 'Simpan' }}</button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold mb-3">Brand</h2>
            <button class="btn btn-primary" @click.prevent="openCreate">Tambah Brand</button>
          </div>

          <div class="mt-2">
            <input v-model="filters.search" @keyup.enter="reload" placeholder="Cari brand..." class="w-full border p-2 rounded" />
          </div>
        </div>

        <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Daftar Brand</h2>

          <div v-if="!brands.data.length" class="text-sm text-muted-foreground">Belum ada data.</div>

          <table v-else class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">Logo</th>
                <th class="text-left p-2">Nama</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="b in brands.data" :key="b.id" class="border-t">
                <td class="p-2">
                  <img v-if="b.logo" :src="`/storage/${b.logo}`" class="h-12 object-contain" />
                </td>
                <td class="p-2">{{ b.name }}</td>
                <td class="p-2 text-center">
                  <button @click="edit(b)" class="mr-2 text-blue-600">Edit</button>
                  <button @click="destroy(b.id)" class="text-red-600">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="brands.prev_page_url" @click="goto(brands.current_page - 1)" class="btn">Previous</button>
            <button v-if="brands.next_page_url" @click="goto(brands.current_page + 1)" class="btn">Next</button>
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

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Brand', href: '/brands' }];

const page = usePage();
type Brand = { id: number; name: string; logo?: string };
type PaginatedBrands = { data: Brand[]; current_page: number; next_page_url: string | null; prev_page_url: string | null };
const propsAny: any = (page.props as any);
const brands = computed<PaginatedBrands>(() => (propsAny.brands as PaginatedBrands) ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null });

const dialogOpen = ref(false);
const editing = ref(false);
const editId = ref<number | null>(null);
const form = reactive({ name: '', logo: null as File | null });
const preview = ref<string | null>(null);
const filters = reactive({ search: '' });

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files || !input.files[0]) return;
  form.logo = input.files[0];
  preview.value = URL.createObjectURL(form.logo);
}

function formatNumber(n: number) {
  return n?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') || '0';
}

function goto(pageNum: number) {
  router.get(route('brands.index', { page: pageNum }));
}

function reload() {
  router.get(route('brands.index', { search: filters.search }));
}

function handleSubmit() {
  const finish = () => {
    clearForm();
    router.get(route('brands.index'));
  };

  const formData = new FormData();
  formData.append('name', form.name);
  if (form.logo) formData.append('logo', form.logo);

  if (editing.value && editId.value) {
    router.patch(route('brands.update', editId.value), formData, { onSuccess: finish });
  } else {
    router.post(route('brands.store'), formData, { onSuccess: finish });
  }
}

function openCreate() {
  clearForm();
  editing.value = false;
  dialogOpen.value = true;
}

function edit(b: any) {
  editing.value = true;
  editId.value = b.id;
  form.name = b.name;
  preview.value = b.logo ? `/storage/${b.logo}` : null;
  dialogOpen.value = true;
}

function destroy(id: number) {
  if (!confirm('Hapus brand ini?')) return;
  router.delete(route('brands.destroy', id), { onSuccess: () => router.get(route('brands.index')) });
}

function clearForm() {
  editing.value = false;
  editId.value = null;
  form.name = '';
  form.logo = null;
  preview.value = null;
  dialogOpen.value = false;
}
</script>

<style scoped>
.btn { padding: 8px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff; cursor: pointer }
.btn-primary { background: #2563eb; color: #fff; border-color: transparent }
</style>
