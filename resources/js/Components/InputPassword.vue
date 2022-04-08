<template>
  <div class="relative p-0 border-0">
    <input
      type="password"
      class="mr-2 px-1 border rounded-md focus:outline-none focus:ring-1 focus:border-none focus:ring-yellow-500 w-full"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      ref="input"
    />
    <div class="absolute top-2 right-4 cursor-pointer">
      <EyeIcon
        v-if="!passwordMode"
        class="h-6"
        @click="showPassword"
        ref="eyeOn"
      />
      <EyeOffIcon
        v-if="passwordMode"
        @click="showPassword"
        class="h-6"
        ref="eyeOff"
      />
    </div>
  </div>
</template>

<script>
import { EyeIcon } from "@heroicons/vue/solid";
import { EyeOffIcon } from "@heroicons/vue/solid";

export default {
  components: {
    EyeIcon,
    EyeOffIcon,
  },
  props: ["modelValue"],

  emits: ["update:modelValue"],

  data() {
    return {
      passwordMode: true,
    };
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    showPassword() {
      const input = this.$refs.input;
      const icon = this.$refs.icon;
      if (input.type == "password") {
        input.type = "text";
        this.passwordMode = false;
      } else if (input.type == "text") {
        input.type = "password";
        this.passwordMode = true;
      }
    },
  },
};
</script>
