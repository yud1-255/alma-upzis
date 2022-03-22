<template>
  <popup-modal ref="popup">
    <h2 class="mt-1 mb-4 text-red-700">{{ title }}</h2>
    <ul class="mt-3 list-none md:list-disc list-inside">
      <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
    </ul>
    <div class="flex space-x-2 mb-2 mt-6">
      <Button @click="hide" class="text-green-100 bg-lime-700">{{
        okButton
      }}</Button>
    </div>
  </popup-modal>
</template>

<script>
import Button from "@/Components/Button.vue";
import PopupModal from "@/Components/PopupModal.vue";

export default {
  name: "ErrorModal",
  components: { PopupModal, Button },

  data() {
    return {
      title: "Tidak bisa diproses",
      errors: [],
      okButton: "Oke",
    };
  },
  methods: {
    show(options = {}) {
      this.errors = options.errors;
      this.$refs.popup.open();
    },

    hide() {
      this.$refs.popup.close();
    },
  },
};
</script>
