<template>
  <Head title="Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Transaksi Zakat</h1>
    </template>
    <div class="py-2 md:py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div
        class="overflow-hidden bg-white shadow-sm sm:rounded-lg px-2 min-h-screen"
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="hidden md:table-header-group">
              <tr>
                <td colspan="4" class="py-4 space-x-2">
                  <Link
                    class="px-2 py-2 text-green-100 bg-lime-700 rounded print:hidden"
                    :href="route('zakat.create')"
                    >Buat Transaksi</Link
                  >
                  <a
                    v-if="can.viewAny"
                    class="px-2 py-2 text-green-100 bg-lime-700 rounded print:hidden"
                    :href="route('zakat.export', ['summary', hijriYear])"
                    >Ekspor ke Excel</a
                  >
                </td>
                <td colspan="5" class="text-right">
                  <Input
                    v-if="can.viewAny"
                    v-model="searchTerm"
                    placeholder="Cari berdasarkan nama"
                    class="p-2"
                  />
                  <select
                    v-if="can.viewAny"
                    v-model="hijriYear"
                    @change="searchTransactions"
                  >
                    <option v-for="hijriYear in hijriYears" :key="hijriYear">
                      {{ hijriYear }}
                    </option>
                  </select>
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
            <thead class="table-row-group md:hidden">
              <tr class="font-bold border-b-2">
                <td class="py-4">Tanggal</td>
                <td>Petugas</td>
                <td>Jumlah (Rp)</td>
              </tr>
            </thead>
            <tbody class="hidden md:table-row-group">
              <tr
                v-for="zakat in zakats.data"
                :key="zakat.id"
                :class="{ 'opacity-25': !zakat.is_active }"
              >
                <td class="px-4 py-2">
                  <Link
                    :href="route('zakat.show', zakat.id)"
                    class="text-green-700"
                  >
                    {{ zakat.transaction_no }}
                  </Link>
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                  {{ zakat.transaction_date }}
                </td>
                <td class="px-4 py-2">
                  {{ zakat.receive_from_name }}
                </td>
                <td class="px-4 py-2">{{ zakat.zakat_pic_name }}</td>
                <td class="px-4 py-2">{{ zakat.hijri_year }}</td>
                <td class="px-4 py-2">{{ zakat.family_head }}</td>
                <td class="text-center">
                  {{ zakat.is_offline_submission ? "Gerai" : "Online" }}
                </td>
                <td class="px-4 py-2 text-right">
                  {{ Number(zakat.total_transfer_rp).toLocaleString("id") }}
                </td>
                <td class="py-2 whitespace-nowrap print:hidden">
                  <div v-if="zakat.is_active">
                    <button
                      v-if="can.confirmPayment"
                      @click="confirmPayment(zakat)"
                      class="text-orange-700 px-2"
                      :class="{ invisible: zakat.zakat_pic != null }"
                    >
                      Konfirmasi
                    </button>
                    <button
                      v-if="can.delete"
                      @click="destroy(zakat)"
                      class="text-red-700 px-2"
                    >
                      Batalkan
                    </button>
                  </div>
                  <div v-else>
                    <span class="px-2">Transaksi dibatalkan</span>
                  </div>
                </td>
              </tr>
            </tbody>
            <tbody
              v-for="zakat in zakats.data"
              :key="zakat.id"
              class="border-b table-row-group md:hidden"
              @click="$inertia.get(route('zakat.show', zakat.id))"
            >
              <tr>
                <td colspan="3" class="font-semibold py-1">
                  {{ zakat.transaction_no }}
                </td>
              </tr>
              <tr>
                <td class="whitespace-nowrap py-1">
                  {{ zakat.transaction_date }}
                </td>
                <td>{{ zakat.zakat_pic_name ?? "-" }}</td>
                <td class="text-right py-1">
                  {{ Number(zakat.total_transfer_rp).toLocaleString("id") }}
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="zakats.data.length == 0">
            <div class="mx-2 md:mx-4 mt-8 md:mt-6 text-sm">
              Anda belum memiliki transaksi zakat. Buat transaksi baru
              <Link
                :href="route('zakat.create')"
                class="text-lime-700 underline"
              >
                di sini</Link
              >. Untuk memperbarui data keluarga,
              <Link
                :href="route('family.create')"
                class="text-lime-700 underline"
              >
                klik di sini</Link
              >.
            </div>
          </div>
        </div>

        <div class="w-full flex">
          <pagination :links="zakats.links" />
        </div>
      </div>

      <Confirmation ref="confirmation"></Confirmation>
      <ErrorModal ref="errorModal"></ErrorModal>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Input from "@/Components/Input.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";
import Confirmation from "@/Components/Confirmation.vue";
import ErrorModal from "@/Components/ErrorModal.vue";

import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Head,
    Link,
    Input,
    Pagination,
    Confirmation,
    ErrorModal,
  },
  props: {
    errors: null,
    zakats: Object,
    can: Object,
    hijriYear: String,
    hijriYears: Array,
  },
  data() {
    return {
      searchTerm: "",
    };
  },
  watch: {
    searchTerm: debounce(function (newValue) {
      this.$inertia.replace(
        this.route("zakat.index", {
          searchTerm: newValue,
          hijriYear: this.hijriYear,
        })
      );
    }, 300),
  },
  methods: {
    searchTransactions() {
      this.$inertia.replace(
        this.route("zakat.index", {
          searchTerm: this.searchTerm,
          hijriYear: this.hijriYear,
        })
      );
    },
    async destroy(zakat) {
      const isConfirmed = await this.$refs.confirmation.show({
        title: "Konfirmasi",
        message: `Batalkan transaksi zakat ${zakat.transaction_no} diterima dari ${zakat.receive_from_name}?`,
        okButton: "Lanjut",
        cancelButton: "Batal",
      });

      if (isConfirmed) {
        this.$inertia.delete(route("zakat.destroy", zakat.id), {
          preserveScroll: true,
          onError: () => {
            // window.scrollTo({ top: 0, left: 0, behavior: "smooth" });
            this.$refs.errorModal.show({
              errors: this.errors,
            });
          },
        });
      }
    },
    async confirmPayment(zakat) {
      const isConfirmed = await this.$refs.confirmation.show({
        title: "Konfirmasi",
        message: `Konfirmasi pembayaran dari ${
          zakat.receive_from_name
        } sejumlah Rp. ${Number(zakat.total_transfer_rp).toLocaleString(
          "id"
        )}?`,
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
