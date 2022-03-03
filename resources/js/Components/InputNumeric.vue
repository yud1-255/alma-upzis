<template>
  <input
    class="mr-2 px-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600"
    v-model="formattedValue"
    @focus="isInputActive = true"
    @blur="isInputActive = false"
    ref="input"
  />
</template>

<script>
export default {
  props: ["modelValue"],

  emits: ["update:modelValue"],

  data() {
    return {
      isInputActive: false,
    };
  },

  computed: {
    formattedValue: {
      get: function () {
        if (this.isInputActive) {
          return this.modelValue;
        } else if (this.modelValue && !isNaN(this.modelValue)) {
          return Number(this.modelValue).toLocaleString("id-ID");
        } else {
          return "";
        }
      },
      set: function (newValue) {
        if (isNaN(newValue) || newValue == "") {
          newValue = null;
        }

        this.$emit("update:modelValue", newValue);
      },
    },
  },
};
</script>
