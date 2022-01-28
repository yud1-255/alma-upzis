<template>
  <Head title="Dashboard" />

  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            You're logged in!

            <ul class="list-disc list-inside">
              <li>
                Lengkapi data keluarga
                <Link :href="route('family.create')">[link]</Link>
              </li>
              <li>
                Mulai pengisian zakat
                <Link :href="route('zakat.create')">[link]</Link>
              </li>
              <li>Transfer dan konfirmasi pembayaran zakat [link:wa]</li>
              <li>
                Lihat transaksi zakat anda
                <Link :href="route('zakat.index')">[link]</Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
  },
};
</script>
