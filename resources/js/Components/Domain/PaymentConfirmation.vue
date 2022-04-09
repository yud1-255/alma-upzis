<template>
  <PopupModal ref="popup">
    <h2 class="mt-1 mb-4">Konfirmasi</h2>
    <form class="pb-4" @submit.prevent="confirmPayment()">
      <div>
        Konfirmasi pembayaran dari
        <span class="font-semibold">{{ zakat.receive_from_name }}</span>
        sejumlah
        <span class="font-semibold"
          >Rp. {{ Number(zakat.total_transfer_rp).toLocaleString("id") }}</span
        >?
      </div>
      <div class="flex my-2">
        <div class="p-2 font-semibold">Tanggal terima pembayaran</div>
        <div class="mx-2">
          <Input id="payment-date" type="date" v-model="paymentDate" />
        </div>
      </div>

      <div>
        Isikan tanggal terima pembayaran dan klik
        <span class="font-semibold">Konfirmasi</span> di bawah untuk
        melanjutkan.
      </div>

      <div class="flex space-x-2 mb-2 mt-4">
        <Button
          @click="close"
          type="button"
          class="text-gray-100 bg-amber-500 mt-4"
          >Batal</Button
        >
        <Button class="text-green-100 bg-lime-700 mt-4">Konfirmasi</Button>
      </div>
    </form>
  </PopupModal>
</template>
<script>
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import PopupModal from "@/Components/PopupModal.vue";

export default {
  components: {
    Label,
    Input,
    Button,
    PopupModal,
  },
  props: {
    zakat: null,
  },
  data() {
    return {
      paymentDate: new Date().toISOString().split("T")[0],
    };
  },
  methods: {
    open() {
      this.$refs.popup.open();
    },

    close() {
      this.$refs.popup.close();
    },
    confirmPayment() {
      this.$inertia.post(
        route(`zakat.confirm`, this.zakat.id),
        {
          paymentDate: this.paymentDate,
          pageUrl: this.$page.url,
        },
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.$refs.popup.close();
          },
        }
      );
    },
  },
};
</script>
