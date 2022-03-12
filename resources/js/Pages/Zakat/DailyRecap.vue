<template>
  <Head title="Rekap Harian"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Rekap Pendapatan Zakat Harian</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="w-full mb-2 flex">
              <div class="w-1/2">
                <a
                  class="px-2 py-2 text-green-100 bg-lime-700 rounded print:hidden"
                  :href="route('zakat.export', 'muzakki_recap')"
                  >Ekspor ke Excel</a
                >
              </div>
              <div class="w-1/2 text-right">
                <select v-model="hijriYear">
                  <option v-for="hijriYear in hijriYears" :key="hijriYear">
                    {{ hijriYear }}
                  </option>
                </select>
              </div>
            </div>
            <table class="w-full">
              <thead class="font-bold border-b-2">
                <tr>
                  <td rowspan="2" class="px-4 py-2">No. Zakat</td>
                  <td rowspan="2" class="px-4 py-2">Muzakki</td>
                  <td colspan="3" class="px-4 py-2">Fitrah</td>
                  <td rowspan="2" class="px-4 py-2">Maal</td>
                  <td rowspan="2" class="px-4 py-2">Profesi</td>
                  <td rowspan="2" class="px-4 py-2">Infaq Shadaqah</td>
                  <td colspan="2" class="px-4 py-2">Fidyah</td>
                  <td rowspan="2" class="px-4 py-2">Wakaf</td>
                  <td rowspan="2" class="px-4 py-2">Kafarat</td>
                  <td rowspan="2" class="px-4 py-2">Tanggal</td>
                  <td rowspan="2" class="px-4 py-2">Terima dari</td>
                  <td rowspan="2" class="px-4 py-2">Petugas</td>
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
                <tr v-for="zakat_line in dailyAgg.zakats" :key="zakat_line.id">
                  <td>
                    {{ zakat_line.transaction_no }}
                  </td>
                  <td>{{ zakat_line.muzakki_name }}</td>
                  <td class="text-right">
                    {{ Number(zakat_line.fitrah_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.fitrah_kg).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.fitrah_lt).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.maal_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.profesi_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.infaq_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.fidyah_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.fidyah_kg).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.wakaf_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right">
                    {{ Number(zakat_line.kafarat_rp).toLocaleString("id") }}
                  </td>
                  <td class="text-right whitespace-nowrap">
                    {{ zakat_line.transaction_date }}
                  </td>
                  <td>{{ zakat_line.receive_from_name }}</td>
                  <td>{{ zakat_line.zakat_pic_name }}</td>
                </tr>
                <tr class="bg-gray-200">
                  <th>
                    {{ dailyAgg.transaction_date }}
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
                  <th class="text-right whitespace-nowrap"></th>
                  <th></th>
                  <th></th>
                </tr>
              </tbody>
            </table>
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
import _ from "lodash";
import debounce from "lodash/debounce";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Input,
  },
  props: {
    zakats: Object,
    hijriYear: String,
    hijriYears: Array,
  },
  watch: {
    hijriYear: debounce(function (newValue) {
      this.$inertia.replace(
        this.route("zakat.dailyRecap", {
          hijriYear: newValue,
        })
      );
    }, 300),
  },
  methods: {
    groupedTable() {
      let dailyAgg = _(this.zakats)
        .groupBy("transaction_date")
        .map((zakats, date) => {
          return {
            transaction_date: date,
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
          };
        })
        .value();

      return dailyAgg;
    },
  },
};
</script>
