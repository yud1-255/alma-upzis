<template>
  <Head title="Rekap Transaksi Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Rekap Transaksi Zakat</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="w-full mb-2 flex">
              <div class="w-1/2">
                <a
                  class="px-2 py-2 text-green-100 bg-lime-700 rounded print:hidden"
                  :href="
                    route('zakat.export', ['transaction_recap', hijriYear])
                  "
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
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="font-bold border-b-2">
                  <tr>
                    <td rowspan="2" class="px-4 py-2">No. Zakat</td>
                    <td rowspan="2" class="px-4 py-2">Terima dari</td>
                    <td colspan="3" class="px-4 py-2">Fitrah</td>
                    <td rowspan="2" class="px-4 py-2">Maal</td>
                    <td rowspan="2" class="px-4 py-2">Profesi</td>
                    <td rowspan="2" class="px-4 py-2">Infaq Shadaqah</td>
                    <td colspan="2" class="px-4 py-2">Fidyah</td>
                    <td rowspan="2" class="px-4 py-2">Wakaf</td>
                    <td rowspan="2" class="px-4 py-2">Kafarat</td>
                    <td rowspan="2" class="px-4 py-2">Biaya Unik</td>
                    <td rowspan="2" class="px-4 py-2">Total (Rp)</td>
                    <td rowspan="2" class="px-4 py-2">Tanggal</td>
                  </tr>
                  <tr>
                    <td>Rp</td>
                    <td>Kg</td>
                    <td>Lt</td>
                    <td>Rp</td>
                    <td>Kg</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="zakat in zakats.data" :key="zakat.id">
                    <td>
                      <Link
                        :href="route('zakat.show', zakat.id)"
                        class="text-green-700"
                        >{{ zakat.transaction_no }}</Link
                      >
                    </td>
                    <td>{{ zakat.receive_from_name }}</td>
                    <td class="text-right">
                      {{ Number(zakat.fitrah_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.fitrah_kg).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.fitrah_lt).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.maal_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.profesi_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.infaq_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.fidyah_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.fidyah_kg).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.wakaf_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.kafarat_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.unique_number).toLocaleString("id") }}
                    </td>
                    <td class="text-right">
                      {{ Number(zakat.total_transfer_rp).toLocaleString("id") }}
                    </td>
                    <td class="text-right whitespace-nowrap">
                      {{ zakat.payment_date ?? zakat.transaction_date }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <pagination :links="zakats.links" />
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
import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Pagination,
    Input,
  },
  props: {
    zakats: Object,
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
        this.route("zakat.transactionRecap", {
          searchTerm: newValue,
          hijriYear: this.hijriYear,
        })
      );
    }, 300),
  },
  methods: {
    searchTransactions() {
      this.$inertia.replace(
        this.route("zakat.transactionRecap", {
          searchTerm: this.searchTerm,
          hijriYear: this.hijriYear,
        })
      );
    },
  },
};
</script>
