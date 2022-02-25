<template>
  <Head title="Rekap Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Pembayaran Online</h1>
      <div class="py-6 bg-white border-b border-gray-200">
        <div class="w-full text-right mb-2">
          <Input v-model="searchTerm" placeholder="Cari berdasarkan nama" />
        </div>
        <table class="w-full">
          <thead class="font-bold border-b-2">
            <td class="px-4 py-2">No. Zakat</td>
            <td class="px-4 py-2">Tanggal</td>
            <td class="px-4 py-2">Terima dari</td>
            <td class="px-4 py-2">Telepon</td>
            <td class="px-4 py-2">Email</td>
            <td class="px-4 py-2">Konfirmasi Petugas</td>
            <td class="px-4 py-2">Jumlah (Rp)</td>
            <td class="px-4 py-2 w-28"></td>
          </thead>
          <tbody>
            <tr v-for="zakat in zakats.data" :key="zakat.id">
              <td>{{ zakat.transaction_no }}</td>
              <td>{{ zakat.transaction_date }}</td>
              <td>{{ zakat.receive_from_name }}</td>
              <td>{{ zakat.receive_from_phone }}</td>
              <td>{{ zakat.receive_from_email }}</td>
              <td>{{ zakat.zakat_pic_name }}</td>
              <td class="text-right">
                {{
                  Number(
                    zakat.total_transfer_rp > 0
                      ? zakat.total_transfer_rp
                      : zakat.total_rp
                  ).toLocaleString("id")
                }}
              </td>
              <td v-if="can.confirmPayment">
                <button
                  v-if="zakat.zakat_pic_name == null"
                  @click="confirmPayment(zakat)"
                  class="text-orange-700"
                >
                  Konfirmasi
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <pagination :links="zakats.links" />

        <confirmation ref="confirmation">></confirmation>
      </div>
    </template>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import Input from "@/Components/Input.vue";
import Pagination from "@/Components/Pagination.vue";
import Confirmation from "@/Components/Confirmation.vue";

import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Input,
    Pagination,
    Confirmation,
  },
  props: {
    zakats: Object,
    can: Object,
  },
  data() {
    return {
      searchTerm: "",
    };
  },
  watch: {
    searchTerm: debounce(function (newValue) {
      this.$inertia.get(
        this.route("zakat.onlinePayments"),
        { searchTerm: newValue },
        {
          preserveState: true,
          preserveScroll: true,
        }
      );
    }, 300),
  },
  methods: {
    async confirmPayment(zakat) {
      const isConfirmed = await this.$refs.confirmation.show({
        title: "Konfirmasi",
        message: `Konfirmasi pembayaran dari ${zakat.receive_from_name} untuk keluarga ${zakat.family_head}?`,
        okButton: "Lanjut",
        cancelButton: "Batal",
      });

      if (isConfirmed) {
        this.$inertia.post(
          route(`zakat.confirm`, zakat.id),
          {
            pageUrl: this.$page.url,
          },
          {
            preserveState: true,
            preserveScroll: true,
          }
        );
      }
    },
  },
};
</script>
