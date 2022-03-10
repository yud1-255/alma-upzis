<template>
  <div
    class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white"
  >
    <div class="border-b-2 border-yellow-200 pb-6 w-full sm:max-w-md">
      <Link href="/">
        <!-- <BreezeApplicationLogo class="w-20 h-20 fill-current text-gray-500" /> -->
        <img src="/assets/yam-logo.png" class="h-20 mx-auto" />
      </Link>
    </div>

    <div
      class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
    >
      <slot />
    </div>
  </div>
</template>

<script>
import BreezeApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeApplicationLogo,
    Link,
  },
};
</script>
