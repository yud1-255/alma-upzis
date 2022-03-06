<template>
  <div class="overflow-x-auto">
    <table class="table-auto">
      <thead class="font-bold border-b-2">
        <tr>
          <th colspan="11" class="text-center py-2">
            <div class="flex sm:hidden print:visible w-full justify-between">
              <img src="/assets/yam-logo-bw.png" class="object-contain h-12" />
              <div class="text-center font-semibold">
                Yayasan Al Munawwarah<br />
                Unit Pengelolaan Zakat, Infaq, dan Shadaqah <br />
                UPZIS Masjid Al Muhajirin
              </div>
              <img
                src="/assets/upzis-logo-bw.png"
                class="object-contain h-12 invisible"
              />
            </div>
            <div class="py-4 font-semibold text-left">
              <p>Transaksi no: {{ zakat.transaction_no }}</p>
              <p>Tanggal: {{ zakat.transaction_date }}</p>
            </div>
          </th>
        </tr>
        <tr>
          <td rowspan="2" class="px-4 py-2">Nama</td>
          <td colspan="3" class="px-4 py-2">Fitrah</td>
          <td rowspan="2" class="px-4 py-2">Maal</td>
          <td rowspan="2" class="px-4 py-2">Profesi</td>
          <td rowspan="2" class="px-4 py-2">Infaq Shadaqah</td>
          <td colspan="2" class="px-4 py-2">Fidyah</td>
          <td rowspan="2" class="px-4 py-2">Wakaf</td>
          <td rowspan="2" class="px-4 py-2">Kafarat</td>
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
        <tr v-for="zakat_line in zakat.zakat_lines" :key="zakat_line.id">
          <td class="px-2">{{ zakat_line.muzakki.name }}</td>
          <td class="text-right">
            {{ formatNumber(zakat_line.fitrah_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.fitrah_kg) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.fitrah_lt) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.maal_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.profesi_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.infaq_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.fidyah_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.fidyah_kg) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.wakaf_rp) }}
          </td>
          <td class="text-right">
            {{ formatNumber(zakat_line.kafarat_rp) }}
          </td>
        </tr>
      </tbody>
      <tfoot class="border-t border-gray-200">
        <tr>
          <th class="text-left">Total</th>
          <th class="text-right">
            {{ formatNumber(totalFitrahRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalFitrahKg()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalFitrahLt()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalMaalRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalProfesiRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalInfaqRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalFidyahRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalFidyahKg()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalWakafRp()) }}
          </th>
          <th class="text-right">
            {{ formatNumber(totalKafaratRp()) }}
          </th>
        </tr>
        <tr class="align-top">
          <td colspan="6" class="pt-12 text-left">
            <p>Terima dari: {{ zakat.receive_from_name }}</p>
            <p>Petugas: {{ zakat.zakat_pic?.name }}</p>
          </td>
          <td colspan="5" class="pt-12 text-right">
            <div>
              <p>Jumlah:</p>
              <p>
                Rp:
                {{
                  formatNumber(
                    totalFitrahRp() +
                      totalMaalRp() +
                      totalProfesiRp() +
                      totalInfaqRp() +
                      totalFidyahRp() +
                      totalWakafRp() +
                      totalKafaratRp()
                  )
                }}
              </p>
              <p>
                Kg:
                {{ formatNumber(totalFitrahKg() + totalFidyahKg()) }}
              </p>
              <p>Lt: {{ formatNumber(totalFitrahLt()) }}</p>
              <div v-if="!zakat.is_offline_submission">
                <div>
                  Biaya Unik:
                  {{ formatNumber(Number(zakat.unique_number, 0)) }}
                </div>
                <div>
                  Total Transfer:
                  {{ formatNumber(Number(zakat.total_transfer_rp, 0)) }}
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr class="sm:hidden print:visible">
          <td colspan="11">
            *) tanda terima diterbitkan secara otomatis oleh aplikasi UPZIS
            <br />
            **) harap simpan tanda terima ini sebagai bukti pembayaran
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>
<script>
export default {
  setup() {},
  props: {
    zakat: Object,
  },
  methods: {
    formatNumber(amount) {
      return Number(amount).toLocaleString("id");
    },
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
    totalInfaqRp() {
      return this.zakat.zakat_lines.reduce((total, line) => {
        total += Number(line.infaq_rp);
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
