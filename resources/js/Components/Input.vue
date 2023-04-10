<template>
  <input
    class="mr-2 px-1 border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:border-none focus:ring-yellow-500"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="input"
  />
</template>

<script>
export default {
  props: ["modelValue"],

  emits: ["update:modelValue"],

  methods: {
    focus() {
      this.$refs.input.focus();
    },
  },
};
</script>
