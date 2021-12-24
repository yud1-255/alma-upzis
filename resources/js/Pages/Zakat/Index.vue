<template>
  <Head title="Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Transaksi Zakat
      </h1>
      <table>
        <thead class="font-bold bg-gray-300 border-b-2">
          <td class="px-4 py-2">No. Zakat</td>
          <td class="px-4 py-2">Tanggal</td>
          <td class="px-4 py-2">Terima dari</td>
          <td class="px-4 py-2">Petugas</td>
          <td class="px-4 py-2">Periode</td>
          <td class="px-4 py-2">Kepala Keluarga</td>
          <td class="px-4 py-2">Action</td>
        </thead>
        <tbody>
          <tr v-for="zakat in zakats.data" :key="zakat.id">
            <td class="px-4 py-2">{{ zakat.transaction_no }}</td>
            <td class="px-4 py-2">{{ zakat.transaction_date }}</td>
            <td class="px-4 py-2">{{ zakat.receive_from_name }}</td>
            <td class="px-4 py-2">{{ zakat.zakat_pic_name }}</td>
            <td class="px-4 py-2">{{ zakat.hijri_year }}</td>
            <td class="px-4 py-2">{{ zakat.family_head }}</td>
            <td class="px-4 py-2 font-extrabold">
              <Link
                :href="route('zakat.edit', zakat.id)"
                class="text-green-700"
              >
                Ubah
              </Link>
              <Link @click="destroy(zakat.id)" class="text-red-700">
                Hapus
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
      <pagination :links="zakats.links" />
    </template>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeNavLink from "@/Components/NavLink.vue";
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
    zakats: Object,
  },
  methods: {
    destroy(id) {
      this.$inertia.delete(route("zakats.destroy"), id);
    },
  },
};
</script>
