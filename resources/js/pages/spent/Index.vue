<template>
  <Head title="Spent" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Dialog v-model:open="editDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit Spent</DialogTitle>
        </DialogHeader>

        <div class="grid gap-3">
          <div>
            <label class="block text-sm mb-1">Tanggal</label>
            <input type="date" v-model="form.tanggal" class="w-full border p-2 rounded" required />
          </div>

          <div>
            <label class="block text-sm mb-1">Nominal</label>
            <input type="number" v-model.number="form.nominal" class="w-full border p-2 rounded" required min="0" />
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
          <h2 class="text-lg font-semibold mb-3">Input Spent</h2>

          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:items-end">
            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Tanggal</label>
              <input type="date" v-model="form.tanggal" class="w-full border p-2 rounded" required />
            </div>

            <div class="sm:col-span-1">
              <label class="block text-sm mb-1">Nominal</label>
              <input type="number" v-model.number="form.nominal" class="w-full border p-2 rounded" required min="0" />
            </div>

            <div class="sm:col-span-1">
              <button type="submit" class="btn btn-primary w-full">{{ editing ? 'Update' : 'Simpan' }}</button>
            </div>
          </form>
        </div>

        <div class="relative min-h-[40vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
          <h2 class="text-lg font-semibold mb-3">Daftar Spent</h2>

          <div v-if="!spents.data.length" class="text-sm text-muted-foreground">Belum ada data.</div>

          <table v-else class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">Tanggal</th>
                <th class="text-right p-2">Nominal</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="s in spents.data" :key="s.id" class="border-t">
                <td class="p-2">{{ formatDate(s.tanggal) }}</td>
                <td class="p-2 text-right">Rp{{ formatNumber(s.nominal) }}</td>
                <td class="p-2 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="edit(s)" class="pill-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M3 21v-4a4 4 0 014-4h6"/></svg>
                      <span class="hidden sm:inline ml-2 text-sm text-white">Edit</span>
                    </button>

                    <button @click="destroy(s.id)" class="pill-btn pill-danger">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 6a1 1 0 10-2 0v6a1 1 0 102 0V8zm6 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                      <span class="hidden sm:inline ml-2 text-sm text-white">Hapus</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="spentsAny.prev_page_url" @click="goto(spentsAny.current_page - 1)" class="pill-btn small">Previous</button>
            <button v-if="spentsAny.next_page_url" @click="goto(spentsAny.current_page + 1)" class="pill-btn small">Next</button>
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
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogClose,
} from '@/components/ui/dialog';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Spent', href: '/spent' }];

const page = usePage();
const spents = computed(() => (page.props.spents ?? { data: [], current_page: 1, next_page_url: null, prev_page_url: null }));
// Template-friendly alias to avoid TS template checking issues
const spentsAny: any = spents;

const editing = ref(false);
const editId = ref<number | null>(null);
const editDialogOpen = ref(false);
const form = reactive({ tanggal: '', nominal: null as number | null });

function goto(pageNum: number) {
  router.get(route('spents.index', { page: pageNum }));
}

function handleSubmit() {
  if (editing.value && editId.value) {
    router.patch(route('spents.update', editId.value), { tanggal: form.tanggal, nominal: form.nominal });
  } else {
    router.post(route('spents.store'), { tanggal: form.tanggal, nominal: form.nominal });
  }
  clearForm();
}

function edit(s: any) {
  editing.value = true;
  editId.value = s.id;
  form.tanggal = toInputDate(s.tanggal);
  form.nominal = s.nominal;
  editDialogOpen.value = true;
}

function destroy(id: number) {
  if (!confirm('Hapus spent ini?')) return;
  router.delete(route('spents.destroy', id));
}

function clearForm() {
  editing.value = false;
  editId.value = null;
  form.tanggal = '';
  form.nominal = null;
  editDialogOpen.value = false;
}

function toInputDate(d: string) {
  if (!d) return '';
  // Accept Date object or timestamp or string
  const date = new Date(d);
  if (isNaN(date.getTime())) return d;
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

function formatNumber(n: number) {
  return n?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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
  .pill-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .9rem; border-radius: 9999px; border: none; background: #6b21a8; color: #fff; cursor: pointer; box-shadow: 0 6px 20px rgba(107,33,168,0.18); transition: transform .08s ease, box-shadow .12s ease }
  .pill-btn.small { padding: .35rem .7rem; font-size: .85rem }
  .pill-btn:hover { transform: translateY(-2px) }
  .pill-btn.pill-danger { background: #dc2626 }
</style>
