<template>
  <Head title="Lihat transaksi zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Transaksi Zakat
      </h1>
    </template>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- {{ zakat }} -->
            <div class="py-4">
              <p>Transaksi no: {{ zakat.transaction_no }}</p>
              <p>Tanggal: {{ zakat.transaction_date }}</p>
            </div>

            <div class="overflow-x-auto">
              <table class="table-auto">
                <thead class="font-bold bg-gray-300 border-b-2">
                  <td class="px-4 py-2">Nama</td>
                  <td class="px-4 py-2">Fitrah (rp kg lt)</td>
                  <td class="px-4 py-2">Maal</td>
                  <td class="px-4 py-2">Profesi</td>
                  <td class="px-4 py-2">Fidyah (rp kg)</td>
                  <td class="px-4 py-2">Wakaf</td>
                  <td class="px-4 py-2">Kafarat</td>
                </thead>
                <tbody>
                  <tr
                    v-for="zakat_line in zakat.zakat_lines"
                    :key="zakat_line.id"
                  >
                    <td>{{ zakat_line.muzakki.name }}</td>
                    <td class="text-right">
                      {{ zakat_line.fitrah_rp }} | {{ zakat_line.fitrah_kg }} |
                      {{ zakat_line.fitrah_lt }}
                    </td>
                    <td class="text-right">{{ zakat_line.maal_rp }}</td>
                    <td class="text-right">{{ zakat_line.profesi_rp }}</td>
                    <td class="text-right">
                      {{ zakat_line.fidyah_rp }} | {{ zakat_line.fidyah_kg }}
                    </td>
                    <td class="text-right">{{ zakat_line.wakaf_rp }}</td>
                    <td class="text-right">{{ zakat_line.kafarat_rp }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <th>Total</th>
                  <th class="text-right">
                    {{ totalFitrahRp() }} | {{ totalFitrahKg() }} |
                    {{ totalFitrahLt() }}
                  </th>
                  <th class="text-right">{{ totalMaalRp() }}</th>
                  <th class="text-right">{{ totalProfesiRp() }}</th>
                  <th class="text-right">
                    {{ totalFidyahRp() }} | {{ totalFidyahKg() }}
                  </th>
                  <th class="text-right">{{ totalWakafRp() }}</th>
                  <th class="text-right">{{ totalKafaratRp() }}</th>
                </tfoot>
              </table>
            </div>
            <div class="flex flex-wrap py-4">
              <div>
                <p>Terima dari: {{ zakat.receive_from.name }}</p>
                <p>Petugas: {{ zakat.zakat_pic?.name }}</p>
              </div>
              <div class="mx-8 md:text-right md:px-64">
                <p>Jumlah:</p>
                <p>
                  Rp:
                  {{
                    totalFitrahRp() +
                    totalMaalRp() +
                    totalProfesiRp() +
                    totalFidyahRp() +
                    totalWakafRp() +
                    totalKafaratRp()
                  }}
                </p>
                <p>Kg: {{ totalFitrahKg() + totalFidyahKg() }}</p>
                <p>Lt: {{ totalFitrahLt() }}</p>
              </div>
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

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
  },
  setup() {},
  props: {
    zakat: Object,
  },
  methods: {
    totalFitrahRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.fitrah_rp);
        return total;
      }, 0);
    },
    totalFitrahKg() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.fitrah_kg);
        return total;
      }, 0);
    },
    totalFitrahLt() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.fitrah_lt);
        return total;
      }, 0);
    },
    totalMaalRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.maal_rp);
        return total;
      }, 0);
    },
    totalProfesiRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.profesi_rp);
        return total;
      }, 0);
    },
    totalFidyahRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.fidyah_rp);
        return total;
      }, 0);
    },
    totalFidyahKg() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.fidyah_kg);
        return total;
      }, 0);
    },
    totalWakafRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.wakaf_rp);
        return total;
      }, 0);
    },
    totalKafaratRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.kafarat_rp);
        return total;
      }, 0);
    },
  },
};
</script>
