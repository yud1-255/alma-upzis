<template>
  <Head title="Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Transaksi Zakat
      </h1>
      <div class="py-6 bg-white border-b border-gray-200">
        <div class="mb-4">
          <Link
            class="px-6 py-2 mb-2 text-green-100 bg-green-500 rounded print:hidden"
            :href="route('zakat.create')"
            >Buat Transaksi</Link
          >
        </div>
        <table>
          <thead class="font-bold bg-gray-300 border-b-2">
            <td class="px-4 py-2">No. Zakat</td>
            <td class="px-4 py-2">Tanggal</td>
            <td class="px-4 py-2">Terima dari</td>
            <td class="px-4 py-2">Petugas</td>
            <td class="px-4 py-2">Periode</td>
            <td class="px-4 py-2">Kepala Keluarga</td>
            <td class="px-4 py-2">Jumlah</td>
            <td class="px-4 py-2 print:hidden"></td>
          </thead>
          <tbody>
            <tr v-for="zakat in zakats.data" :key="zakat.id">
              <td class="px-4 py-2">{{ zakat.transaction_no }}</td>
              <td class="px-4 py-2">{{ zakat.transaction_date }}</td>
              <td class="px-4 py-2">{{ zakat.receive_from_name }}</td>
              <td class="px-4 py-2">{{ zakat.zakat_pic_name }}</td>
              <td class="px-4 py-2">{{ zakat.hijri_year }}</td>
              <td class="px-4 py-2">{{ zakat.family_head }}</td>
              <td class="px-4 py-2">{{ zakat.total_rp.toLocaleString() }}</td>
              <td class="px-4 py-2 font-extrabold print:hidden">
                <Link
                  :href="route('zakat.show', zakat.id)"
                  class="text-green-700"
                >
                  Lihat
                </Link>
                <Link
                  v-if="can.confirmPayment"
                  @click="confirmPayment(zakat.id)"
                  class="text-orange-700"
                >
                  Konfirmasi
                </Link>
                <Link
                  v-if="can.delete"
                  @click="destroy(zakat.id)"
                  class="text-red-700"
                >
                  Hapus
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
    can: Object,
  },
  methods: {
    destroy(id) {
      this.$inertia.delete(route("zakat.destroy", id), {
        preserveScroll: true,
      });
    },
    confirmPayment(id) {
      this.$inertia.post(
        route(`zakat.confirm`, id),
        {
          pageUrl: this.$page.url,
        },
        {
          preserveState: true,
          preserveScroll: true,
        }
      );
    },
  },
};
</script>
