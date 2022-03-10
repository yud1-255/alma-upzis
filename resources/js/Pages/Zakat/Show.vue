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
    <div class="py-6 print:text-xs">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200 print:font-mono">
            <ZakatReceipt :zakat="zakat"></ZakatReceipt>

            <div v-if="printWithCopy" class="hidden print:block">
              <div class="py-4 text-center">
                sisi untuk muzakki
                <hr />
                sisi untuk petugas
              </div>

              <ZakatReceipt :zakat="zakat"></ZakatReceipt>
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
                        Number(zakat.total_transfer_rp, 0).toLocaleString("id")
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
                @click="print(false)"
              >
                Cetak
              </Button>
              <Button
                v-if="can.print && zakat.zakat_pic != null"
                @click="print(true)"
              >
                Cetak Rangkap
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

import ZakatReceipt from "@/Components/Domain/ZakatReceipt.vue";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Button,
    PopupModal,
    ZakatReceipt,
  },
  setup() {},
  props: {
    zakat: Object,
    bankAccount: String,
    displayBankAccount: Boolean,
    displayQRIS: Boolean,
    can: Object,
  },
  data() {
    return {
      printWithCopy: false,
    };
  },
  methods: {
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
    print(printWithCopy) {
      this.printWithCopy = printWithCopy;
      this.$nextTick(function () {
        window.print();
      });
    },
  },
};
</script>
