<template>
  <Head title="Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Transaksi Zakat</h1>
    </template>
    <div class="py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg px-2">
        <table class="w-full">
          <thead>
            <tr v-if="can.viewAny">
              <td colspan="4" class="py-4 space-x-2">
                <Link
                  class="px-2 py-2 text-green-100 bg-green-500 rounded print:hidden"
                  :href="route('zakat.create')"
                  >Buat Transaksi</Link
                >
                <a
                  class="px-2 py-2 text-green-100 bg-green-500 rounded print:hidden"
                  :href="route('zakat.export', 'summary')"
                  >Ekspor ke Excel</a
                >
              </td>
              <td colspan="5" class="text-right">
                <Input
                  v-model="searchTerm"
                  placeholder="Cari berdasarkan nama"
                />
              </td>
            </tr>
            <tr class="font-bold border-b-2">
              <td class="px-4 py-2">No. Zakat</td>
              <td class="px-4 py-2">Tanggal</td>
              <td class="px-4 py-2">Terima dari</td>
              <td class="px-4 py-2">Petugas</td>
              <td class="px-4 py-2">Periode</td>
              <td class="px-4 py-2">Kepala Keluarga</td>
              <td class="px-4 py-2 w-24 text-center">Terima lewat</td>
              <td class="px-4 py-2">Jumlah (Rp)</td>
              <td class="px-4 py-2"></td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="zakat in zakats.data" :key="zakat.id">
              <td class="px-4 py-2">{{ zakat.transaction_no }}</td>
              <td class="px-4 py-2">{{ zakat.transaction_date }}</td>
              <td class="px-4 py-2">
                {{
                  zakat.is_offline_submission
                    ? zakat.receive_from_name
                    : zakat.receive_from_user_name
                }}
              </td>
              <td class="px-4 py-2">{{ zakat.zakat_pic_name }}</td>
              <td class="px-4 py-2">{{ zakat.hijri_year }}</td>
              <td class="px-4 py-2">{{ zakat.family_head }}</td>
              <td class="text-center">
                {{ zakat.is_offline_submission ? "Gerai" : "Online" }}
              </td>
              <td class="px-4 py-2 text-right">
                {{ Number(zakat.total_rp).toLocaleString("id") }}
              </td>
              <td class="px-4 py-2 print:hidden">
                <Link
                  :href="route('zakat.show', zakat.id)"
                  class="text-green-700"
                >
                  Lihat
                </Link>
                <button
                  v-if="can.confirmPayment"
                  @click="confirmPayment(zakat)"
                  class="text-orange-700 mx-2"
                  :class="{ invisible: zakat.zakat_pic == null }"
                >
                  Konfirmasi
                </button>
                <button
                  v-if="can.delete"
                  @click="destroy(zakat)"
                  class="text-red-700"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="w-full flex">
          <pagination :links="zakats.links" />
        </div>
      </div>

      <confirmation ref="confirmation">></confirmation>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
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
      this.$inertia.replace(
        this.route("zakat.index", { searchTerm: newValue })
      );
    }, 300),
  },
  methods: {
    async destroy(zakat) {
      const isConfirmed = await this.$refs.confirmation.show({
        title: "Konfirmasi",
        message: `Hapus zakat dari ${zakat.receive_from_name} untuk keluarga ${zakat.family_head}?`,
        okButton: "Lanjut",
        cancelButton: "Batal",
      });

      if (isConfirmed) {
        this.$inertia.delete(route("zakat.destroy", zakat.id), {
          preserveScroll: true,
        });
      }
    },
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
