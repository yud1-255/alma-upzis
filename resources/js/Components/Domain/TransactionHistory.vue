<template>
  <PopupModal ref="popup">
    <h2 class="mt-1 mb-4">Histori Transaksi - {{ zakat.transaction_no }}</h2>

    <table class="table-auto w-full">
      <thead>
        <th class="text-left">Pengguna</th>
        <th class="w-80 text-left">Aksi</th>
        <th class="w-50 text-left">Waktu</th>
      </thead>

      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.user_name }}</td>
          <td>{{ log.message }}</td>
          <td>
            {{ log.created_at.split("T")[0] }}
            {{ new Date(log.created_at).toLocaleTimeString("en-gb") }}
          </td>
        </tr>
      </tbody>
    </table>
    <div class="flex space-x-2 mb-2 mt-8">
      <Button
        @click="close"
        type="button"
        class="text-green-100 bg-lime-700 mt-4"
        >Tutup</Button
      >
    </div>
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
    logs: null,
  },
  data() {
    return {};
  },
  methods: {
    open() {
      this.$refs.popup.open();
    },

    close() {
      this.$refs.popup.close();
    },
  },
};
</script>
