<template>
  <button
    :type="type"
    class="px-4 py-2 bg-lime-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-lime-600 active:bg-lime-800 focus:outline-none focus:border-lime-800 focus:shadow-outline-gray transition ease-in-out duration-150"
  >
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: "submit",
    },
  },
};
</script>
