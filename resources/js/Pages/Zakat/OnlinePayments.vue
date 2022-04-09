<template>
  <Head title="Pembayaran Online"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Pembayaran Online</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="w-full mb-4 flex">
              <div class="w-1/2">
                <a
                  class="px-2 py-2 text-green-100 bg-lime-700 rounded print:hidden"
                  :href="route('zakat.export', ['online_payments', hijriYear])"
                  >Ekspor ke Excel</a
                >
              </div>

              <div class="w-1/2 text-right">
                <Input
                  v-model="searchTerm"
                  placeholder="Cari berdasarkan nama"
                  class="p-2"
                />
                <select v-model="hijriYear" @change="searchTransactions">
                  <option v-for="hijriYear in hijriYears" :key="hijriYear">
                    {{ hijriYear }}
                  </option>
                </select>
              </div>
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
                  <td>
                    <Link
                      :href="route('zakat.show', zakat.id)"
                      class="text-green-700"
                    >
                      {{ zakat.transaction_no }}
                    </Link>
                  </td>
                  <td>{{ zakat.payment_date ?? zakat.transaction_date }}</td>
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

            <PaymentConfirmation
              :zakat="zakatToConfirm"
              ref="paymentConfirmation"
            />
            <Confirmation ref="confirmation">></Confirmation>
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
import Input from "@/Components/Input.vue";
import Pagination from "@/Components/Pagination.vue";
import Confirmation from "@/Components/Confirmation.vue";
import PaymentConfirmation from "@/Components/Domain/PaymentConfirmation.vue";

import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Input,
    Pagination,
    Confirmation,
    PaymentConfirmation,
  },
  props: {
    zakats: Object,
    can: Object,
    hijriYear: String,
    hijriYears: Array,
  },
  data() {
    return {
      searchTerm: "",
      zakatToConfirm: null,
    };
  },
  watch: {
    searchTerm: debounce(function (newValue) {
      this.$inertia.get(
        this.route("zakat.onlinePayments"),
        { searchTerm: newValue, hijriYear: this.hijriYear },
        {
          preserveState: true,
          preserveScroll: true,
        }
      );
    }, 300),
  },
  methods: {
    searchTransactions() {
      this.$inertia.replace(
        this.route("zakat.onlinePayments", {
          searchTerm: this.searchTerm,
          hijriYear: this.hijriYear,
        })
      );
    },
    async confirmPayment(zakat) {
      this.zakatToConfirm = zakat;
      this.$refs.paymentConfirmation.open();
    },
  },
};
</script>
