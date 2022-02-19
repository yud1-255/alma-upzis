<template>
  <Head title="Rekap Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Pembayaran Online</h1>
      <div class="py-6 bg-white border-b border-gray-200">
        <table>
          <thead class="font-bold bg-gray-300 border-b-2">
            <td class="px-4 py-2">No. Zakat</td>
            <td class="px-4 py-2">Tanggal</td>
            <td class="px-4 py-2">Terima dari</td>
            <td class="px-4 py-2">Telepon</td>
            <td class="px-4 py-2">Email</td>
            <td class="px-4 py-2">Konfirmasi Petugas</td>
            <td class="px-4 py-2">Jumlah (Rp)</td>
            <td class="px-4 py-2"></td>
          </thead>
          <tbody>
            <tr v-for="zakat in zakats.data" :key="zakat.id">
              <td class="px-4 py-2">{{ zakat.transaction_no }}</td>
              <td class="px-4 py-2">{{ zakat.transaction_date }}</td>
              <td class="px-4 py-2">{{ zakat.receive_from_name }}</td>
              <td class="px-4 py-2">{{ zakat.receive_from_phone }}</td>
              <td class="px-4 py-2">{{ zakat.receive_from_email }}</td>
              <td class="px-4 py-2">{{ zakat.zakat_pic_name }}</td>
              <td class="px-4 py-2 text-right">
                {{
                  Number(
                    zakat.total_transfer_rp > 0
                      ? zakat.total_transfer_rp
                      : zakat.total_rp
                  ).toLocaleString("id")
                }}
              </td>
              <td v-if="can.confirmPayment" class="px-4 py-2">
                <Link
                  v-if="zakat.zakat_pic_name == null"
                  @click="confirmPayment(zakat.id)"
                  class="text-orange-700"
                >
                  Konfirmasi
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
        <pagination :links="zakats.links" />
      </div>
    </template>
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
    zakats: Object,
    can: Object,
  },
  methods: {
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
