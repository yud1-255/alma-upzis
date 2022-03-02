<template>
  <Head title="Lihat transaksi zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1
        class="text-xl font-semibold leading-tight text-gray-800 print:hidden"
      >
        Transaksi Zakat
      </h1>
    </template>
    <!-- TODO refactor into printable component -->
    <div class="py-6 print:text-xs">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200 print:font-mono">
            <div class="py-4 font-semibold">
              <p>Transaksi no: {{ zakat.transaction_no }}</p>
              <p>Tanggal: {{ zakat.transaction_date }}</p>
            </div>

            <div class="overflow-x-auto">
              <table class="table-auto">
                <thead class="font-bold border-b-2">
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
                  <tr
                    v-for="zakat_line in zakat.zakat_lines"
                    :key="zakat_line.id"
                  >
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
                            {{
                              formatNumber(Number(zakat.total_transfer_rp, 0))
                            }}
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- <div v-if="!(can.print && can.confirmPayment)"> -->
            <div class="print:hidden">
              <div class="my-4">
                <p class="text-sm font-semibold my-2">Catatan untuk muzakki:</p>
                <ol class="list-decimal list-inside text-sm">
                  <li>
                    Silakan transfer senilai
                    <span class="font-semibold"
                      >Rp.
                      {{
                        formatNumber(Number(zakat.total_transfer_rp, 0))
                      }}</span
                    >
                    melalui salah satu opsi:
                    <ul class="list-disc list-inside ml-4">
                      <li v-if="displayBankAccount">
                        Transfer bank melalui rekening
                        <span class="font-semibold">{{ bankAccount }}</span>
                      </li>
                      <li v-if="displayQRIS">
                        Kirim lewat QRIS
                        <span
                          @click="this.$refs.popup.open()"
                          class="cursor-pointer text-green-600 ml-2"
                          >Tampilkan</span
                        >
                      </li>
                      <li v-if="!displayQRIS && !displayBankAccount">
                        Periode transfer saat ini telah ditutup. Untuk
                        keterangan lebih lanjut, hubungi petugas zakat UPZIS
                        Al-Muhajirin
                      </li>
                    </ul>
                  </li>
                  <li>
                    Kirimkan bukti transfer dan konfirmasi kepada petugas
                    <a
                      href="http://bit.ly/sayazakat"
                      target="_blank"
                      class="cursor-pointer text-green-600 ml-2"
                      >WhatsApp</a
                    >
                  </li>
                  <li>
                    Dana yang dikirimkan tanpa konfirmasi akan kami terima
                    sebagai sedekah dan dimanfaatkan sesuai syariat
                  </li>
                </ol>
                <popup-modal ref="popup">
                  <div class="text-center">
                    <h2>QRIS UPZIS Al-Muhajirin</h2>
                    <img
                      src="/assets/qr-alma.png"
                      class="mx-auto"
                      width="250"
                    />
                    <Button class="mt-6" @click="this.$refs.popup.close()"
                      >Tutup</Button
                    >
                  </div>
                </popup-modal>
              </div>
            </div>
            <div
              v-if="can.print || can.confirmPayment"
              class="pt-6 space-x-2 print:hidden"
            >
              <Button
                v-if="can.confirmPayment"
                @click="confirmPayment(zakat.id)"
              >
                Konfirmasi Pembayaran
              </Button>

              <Button
                v-if="can.print && zakat.zakat_pic != null"
                @click="print()"
              >
                Cetak
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import PopupModal from "@/Components/PopupModal.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Button,
    PopupModal,
  },
  setup() {},
  props: {
    zakat: Object,
    bankAccount: String,
    displayBankAccount: Boolean,
    displayQRIS: Boolean,
    can: Object,
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
    print() {
      window.print();
    },
  },
};
</script>
