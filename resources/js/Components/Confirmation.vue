<template>
  <popup-modal ref="popup">
    <h2>{{ title }}</h2>
    <p>{{ message }}</p>

    <div class="flex space-x-2 mb-2 mt-6">
      <Button @click="cancel" class="text-gray-100 bg-amber-500">{{
        cancelButton
      }}</Button>
      <Button @click="confirm" class="text-green-100 bg-lime-700">{{
        okButton
      }}</Button>
    </div>
  </popup-modal>
</template>

<script>
import Button from "@/Components/Button.vue";
import PopupModal from "@/Components/PopupModal.vue";

export default {
  name: "Confirmation",
  components: { PopupModal, Button },

  data() {
    return {
      title: "",
      message: "",
      okButton: "Lanjut",
      cancelButton: "Batal",
      resolvePromise: undefined,
      rejectPromise: undefined,
    };
  },
  methods: {
    show(options = {}) {
      this.title = options.title;
      this.message = options.message;
      this.okButton = options.okButton;
      if (options.cancelButton) {
        this.cancelButton = options.cancelButton;
      }

      this.$refs.popup.open();

      return new Promise((resolve, reject) => {
        this.resolvePromise = resolve;
        this.rejectPromise = reject;
      });
    },

    confirm() {
      this.$refs.popup.close();
      this.resolvePromise(true);
    },
    cancel() {
      this.$refs.popup.close();
      this.resolvePromise(false);
    },
  },
};
</script>
