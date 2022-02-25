<template>
  <Head title="Muzakki"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Daftar Muzakki</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-2">
              <a
                class="px-2 py-2 text-green-100 bg-green-500 rounded print:hidden"
                :href="route('zakat.export', 'muzakki_list')"
                >Ekspor ke Excel</a
              >
            </div>
            <table class="w-full">
              <thead class="font-bold border-b-2">
                <tr>
                  <td rowspan="2" class="px-4 py-2">Kepala Keluarga</td>
                  <td rowspan="2" class="px-4 py-2">Alamat</td>
                  <td rowspan="2" class="px-4 py-2">Kontak</td>
                  <td rowspan="2" class="px-4 py-2">Muzakki</td>
                  <td colspan="2" class="px-4 py-2">Akun Pengguna</td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td>Nama</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="family in families.data" :key="family.id">
                  <td class="align-top">{{ family.head_of_family }}</td>
                  <td class="align-top">{{ family.address }}</td>
                  <td class="align-top">{{ family.phone }}</td>
                  <td class="align-top">
                    <ul>
                      <li v-for="muzakki in family.muzakkis" :key="muzakki.id">
                        {{ muzakki.name }}
                      </li>
                    </ul>
                  </td>
                  <td class="align-top">{{ family.user?.email }}</td>
                  <td class="align-top">{{ family.user?.name }}</td>
                </tr>
              </tbody>
            </table>
            <pagination :links="families.links" />
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Pagination,
  },
  props: {
    families: Object,
  },
};
</script>
