<template>
  <Head :title="zakat.transaction_no"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1
        class="text-xl font-semibold leading-tight text-gray-800 print:hidden"
      >
        Transaksi Zakat
      </h1>
    </template>

    <div class="py-2 md:py-6 print:text-xs">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200 print:font-mono">
            <div class="hidden md:block print:block">
              <ZakatReceipt :zakat="zakat"></ZakatReceipt>

              <div v-if="printWithCopy" class="hidden print:block">
                <div class="py-4 text-center">
                  sisi untuk muzakki
                  <hr />
                  sisi untuk petugas
                </div>

                <ZakatReceipt :zakat="zakat"></ZakatReceipt>
              </div>
            </div>

            <div class="block md:hidden print:hidden">
              <div class="pb-4 font-semibold text-left">
                <h2>
                  Transaksi no: {{ zakat.transaction_no }}
                  <span v-if="!zakat.is_active" class="text-red-700">
                    &middot; dibatalkan &middot;
                  </span>
                </h2>
              </div>
              <table class="table-auto w-full">
                <thead>
                  <th class="text-left px-0">Muzakki</th>
                  <th class="px-0">Kg</th>
                  <th class="px-0">Lt</th>
                  <th class="px-0">Rp</th>
                </thead>

                <tbody>
                  <tr
                    v-for="zakat_line in zakat.zakat_lines"
                    :key="zakat_line.id"
                  >
                    <td class="px-0">{{ zakat_line.muzakki.name }}</td>
                    <td class="px-0 text-right">
                      {{
                        (
                          Number(zakat_line.fitrah_kg) +
                          Number(zakat_line.fidyah_kg)
                        ).toLocaleString("id")
                      }}
                    </td>
                    <td class="px-0 text-right">
                      {{ Number(zakat_line.fitrah_lt).toLocaleString("id") }}
                    </td>
                    <td class="px-0 text-right">
                      {{
                        (
                          Number(zakat_line.fitrah_rp) +
                          Number(zakat_line.maal_rp) +
                          Number(zakat_line.profesi_rp) +
                          Number(zakat_line.infaq_rp) +
                          Number(zakat_line.fidyah_rp) +
                          Number(zakat_line.wakaf_rp) +
                          Number(zakat_line.kafarat_rp)
                        ).toLocaleString("id")
                      }}
                    </td>
                  </tr>
                </tbody>
                <tfoot class="border-t border-gray-200">
                  <tr v-if="!zakat.is_offline_submission">
                    <td class="px-0 font-semibold">Biaya Unik</td>
                    <td class="px-0"></td>
                    <td class="px-0"></td>
                    <td class="px-0 text-right">
                      {{ Number(zakat.unique_number).toLocaleString("id") }}
                    </td>
                  </tr>
                  <tr>
                    <td class="px-0 font-semibold">Total (Rp)</td>
                    <td class="px-0 text-right">-</td>
                    <td class="px-0 text-right">-</td>
                    <td class="px-0 text-right">
                      {{ Number(zakat.total_transfer_rp).toLocaleString("id") }}
                    </td>
                  </tr>
                </tfoot>
              </table>
              <div class="mt-6 px-0 text-left grid grid-rows-4 grid-cols-2">
                <div>Tanggal:</div>
                <div class="text-right">{{ zakat.transaction_date }}</div>

                <div>Terima dari:</div>
                <div class="text-right">{{ zakat.receive_from_name }}</div>
                <div>Petugas:</div>
                <div class="text-right">{{ zakat.zakat_pic?.name }}</div>
              </div>
            </div>

            <div class="mt-2">
              <p class="text-sm mt-2 mb-12 md:mx-2" v-if="logs.length > 0">
                Terakhir diubah pada
                {{
                  new Date(logs[logs.length - 1].created_at).toLocaleString(
                    "id",
                    {
                      day: "numeric",
                      month: "long",
                      year: "numeric",
                      hour: "numeric",
                      minute: "numeric",
                    }
                  )
                }}
                <button
                  @click="$refs.transactionHistory.open()"
                  class="text-lime-700 text-xs inline mx-2 cursor-pointer"
                >
                  <span class="inline mr-1">Histori</span
                  ><ClockIcon class="h-5 ml-1 inline" />
                </button>
              </p>
              <TransactionHistory
                :zakat="zakat"
                :logs="logs"
                ref="transactionHistory"
              />
            </div>
            <div v-if="zakat.is_active">
              <div v-if="zakat.zakat_pic == null" class="print:hidden">
                <div class="my-4">
                  <p class="text-sm font-semibold my-2">
                    Catatan untuk muzakki:
                  </p>
                  <ol
                    v-if="Number(zakat.total_transfer_rp) > 0"
                    class="list-decimal list-outside ml-4 text-sm"
                  >
                    <li>
                      Silakan transfer senilai
                      <span class="font-semibold"
                        >Rp.
                        {{
                          Number(zakat.total_transfer_rp, 0).toLocaleString(
                            "id"
                          )
                        }}</span
                      >
                      <button
                        class="text-lime-700 text-xs inline mx-1 focus:opacity-75"
                        title="Salin"
                        @click="
                          copyNumberToClipboard(
                            Number(zakat.total_transfer_rp).toString()
                          )
                        "
                      >
                        <span class="inline">Salin</span>
                        <DocumentDuplicateIcon class="h-5 ml-1 inline" />
                      </button>
                      melalui salah satu opsi:
                      <ul class="list-disc list-outside ml-4">
                        <li v-if="displayBankAccount">
                          Transfer bank melalui rekening
                          <span class="font-semibold">{{ bankAccount }} </span>
                          <button
                            class="text-lime-700 text-xs inline mx-2 focus:opacity-75"
                            title="Salin"
                            @click="copyNumberToClipboard(bankAccount)"
                          >
                            <span class="inline">Salin</span>
                            <DocumentDuplicateIcon class="h-5 md:ml-1 inline" />
                          </button>
                        </li>
                        <li v-if="displayQRIS">
                          Kirim lewat QRIS
                          <div
                            @click="this.$refs.popup.open()"
                            class="text-lime-700 text-xs inline mx-2 cursor-pointer"
                          >
                            <span class="inline mr-1">Tampilkan</span
                            ><CursorClickIcon class="h-5 md:ml-1 inline" />
                          </div>
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
                        class="text-lime-700 text-xs inline mx-1 cursor-pointer whitespace-nowrap"
                        >WhatsApp<ExternalLinkIcon class="h-5 ml-1 inline"
                      /></a>
                    </li>
                    <li>
                      Biaya Unik dipergunakan untuk keperluan identifikasi,
                      diperhitungkan dan dikelola sebagai sedekah dari muzakki
                    </li>
                    <li>
                      Dana yang dikirimkan tanpa konfirmasi akan kami terima
                      sebagai sedekah dan dimanfaatkan sesuai syariat
                    </li>
                  </ol>
                  <ol v-else class="list-decimal list-outside ml-4 text-sm">
                    <li>
                      Silakan kirimkan zakat dalam bentuk beras sesuai isian ke
                      gerai zakat di masjid Al Muhajirin BPI
                    </li>
                    <li>
                      Tanda terima akan dicetak setelah kiriman diterima oleh
                      petugas di gerai zakat
                    </li>
                  </ol>
                  <popup-modal ref="popup">
                    <div class="text-center">
                      <h2>QRIS UPZIS Al-Muhajirin</h2>
                      <img
                        src="/assets/qris-alma.jpeg"
                        class="mx-auto"
                        width="250"
                      />

                      <p class="font-bold">Bank Syariah Indonesia</p>
                      <p class="font-bold">NMID: ID2020032834356A01</p>
                      <p class="font-bold">Al Muhajirin BPI Zakat</p>
                      <Button class="mt-6" @click="this.$refs.popup.close()"
                        >Tutup</Button
                      >
                    </div>
                  </popup-modal>
                </div>
              </div>
              <div v-else class="mb-4 print:hidden">
                <p class="text-sm font-semibold my-2">Catatan untuk muzakki:</p>

                <p class="text-sm">
                  Pembayaran telah dikonfirmasi untuk transaksi ini. Silakan
                  menghubungi panitia untuk mendapatkan tanda terima.
                </p>
              </div>
            </div>
            <div
              v-if="(can.print || can.confirmPayment) && zakat.is_active"
              class="pt-6 space-y-1 md:space-x-2 print:hidden"
            >
              <Button
                v-if="can.confirmPayment && zakat.zakat_pic == null"
                class="w-full md:w-auto"
                @click="confirmPayment(zakat)"
              >
                Konfirmasi Pembayaran
              </Button>

              <Button
                v-if="can.print && zakat.zakat_pic != null"
                class="w-full md:w-auto"
                @click="print(false)"
              >
                Cetak
              </Button>
              <Button
                v-if="can.print && zakat.zakat_pic != null"
                class="w-full md:w-auto"
                @click="print(true)"
              >
                Cetak Rangkap
              </Button>
              <Confirmation ref="confirmation"></Confirmation>
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
import Confirmation from "@/Components/Confirmation.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";

import { DocumentDuplicateIcon } from "@heroicons/vue/solid";
import { CursorClickIcon } from "@heroicons/vue/solid";
import { ExternalLinkIcon } from "@heroicons/vue/solid";
import { ClockIcon } from "@heroicons/vue/solid";

import ZakatReceipt from "@/Components/Domain/ZakatReceipt.vue";
import TransactionHistory from "@/Components/Domain/TransactionHistory.vue";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Button,
    PopupModal,
    Confirmation,
    DocumentDuplicateIcon,
    CursorClickIcon,
    ExternalLinkIcon,
    ClockIcon,
    ZakatReceipt,
    TransactionHistory,
  },
  setup() {},
  props: {
    zakat: Object,
    logs: Array,
    bankAccount: String,
    displayBankAccount: Boolean,
    displayQRIS: Boolean,
    can: Object,
  },
  data() {
    return {
      printWithCopy: false,
      paymentDate: new Date().toISOString().split("T")[0],
    };
  },
  methods: {
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
            paymentDate: this.paymentDate,
            pageUrl: this.$page.url,
          },
          {
            preserveState: true,
            preserveScroll: true,
          }
        );
      }
    },
    print(printWithCopy) {
      this.printWithCopy = printWithCopy;
      this.$nextTick(function () {
        window.print();
      });
    },
    copyNumberToClipboard(textWithNumbers) {
      const extractedNumbers = textWithNumbers.match(/\d/g).join("");
      navigator.clipboard.writeText(extractedNumbers);
    },
  },
};
</script>
