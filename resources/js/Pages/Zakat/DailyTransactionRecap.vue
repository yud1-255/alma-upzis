<template>
  <Head title="Rekap Transaksi Harian"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Rekap Transaksi Zakat Harian</h1>
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
                <tbody
                  v-for="dailyAgg in groupedTable()"
                  :key="dailyAgg.transaction_date"
                >
                  <tr v-for="zakat in dailyAgg.zakats" :key="zakat.id">
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
                  <tr class="bg-gray-200">
                    <th>
                      {{ dailyAgg.payment_date }}
                    </th>
                    <th>{{ dailyAgg.sum_muzakki_name }}</th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_fitrah_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_fitrah_kg).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_fitrah_lt).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_maal_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_profesi_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_infaq_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_fidyah_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_fidyah_kg).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_wakaf_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{ Number(dailyAgg.sum_kafarat_rp).toLocaleString("id") }}
                    </th>
                    <th class="text-right">
                      {{
                        Number(dailyAgg.sum_unique_number).toLocaleString("id")
                      }}
                    </th>
                    <th class="text-right">
                      {{
                        Number(dailyAgg.sum_total_transfer_rp).toLocaleString(
                          "id"
                        )
                      }}
                    </th>
                    <th></th>
                  </tr>
                </tbody>
              </table>
            </div>
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
import _ from "lodash";
import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
  },
  props: {
    zakats: Object,
    hijriYear: String,
    hijriYears: Array,
  },
  watch: {
    hijriYear: debounce(function (newValue) {
      this.$inertia.replace(
        this.route("zakat.dailyTransactionRecap", {
          hijriYear: newValue,
        })
      );
    }, 600),
  },
  methods: {
    groupedTable() {
      let dailyAgg = _(this.zakats)
        .groupBy("payment_date")
        .map((zakats, date) => {
          return {
            payment_date: date,
            zakats: zakats,
            sum_fitrah_rp: _.sumBy(zakats, (line) => Number(line.fitrah_rp)),
            sum_fitrah_kg: _.sumBy(zakats, (line) => Number(line.fitrah_kg)),
            sum_fitrah_lt: _.sumBy(zakats, (line) => Number(line.fitrah_lt)),
            sum_maal_rp: _.sumBy(zakats, (line) => Number(line.maal_rp)),
            sum_profesi_rp: _.sumBy(zakats, (line) => Number(line.profesi_rp)),
            sum_infaq_rp: _.sumBy(zakats, (line) => Number(line.infaq_rp)),
            sum_fidyah_rp: _.sumBy(zakats, (line) => Number(line.fidyah_rp)),
            sum_fidyah_kg: _.sumBy(zakats, (line) => Number(line.fidyah_kg)),
            sum_wakaf_rp: _.sumBy(zakats, (line) => Number(line.wakaf_rp)),
            sum_kafarat_rp: _.sumBy(zakats, (line) => Number(line.kafarat_rp)),
            sum_unique_number: _.sumBy(zakats, (line) =>
              Number(line.unique_number)
            ),
            sum_total_transfer_rp: _.sumBy(zakats, (line) =>
              Number(line.total_transfer_rp)
            ),
          };
        })
        .value();

      return dailyAgg;
    },
  },
};
</script>
