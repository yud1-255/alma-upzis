<template>
  <Head title="Beranda" />

  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Assalamualaikum, Muzakki</h1>
    </template>

    <div class="my-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <p class="pt-2 pb-4">
              Selamat datang di aplikasi zakat UPZIS Al Munawwarah/Masjid Al
              Muhajirin. Proses zakat anda dalam empat langkah mudah:
            </p>

            <ol class="list-none list-inside ml-4">
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  1
                </div>
                <div class="ml-2 p-1">
                  Lengkapi data keluarga
                  <Link
                    :href="route('family.create')"
                    class="text-lime-700 ml-2"
                    >[klik di sini]</Link
                  >
                </div>
              </li>
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  2
                </div>
                <div class="ml-2 p-1">
                  Mulai pengisian zakat
                  <Link :href="route('zakat.create')" class="text-lime-700 ml-2"
                    >[klik di sini]</Link
                  >
                </div>
              </li>
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  3
                </div>

                <div class="ml-2 p-1">
                  Transfer dan konfirmasi pembayaran zakat
                  <a
                    href="http://bit.ly/sayazakat"
                    target="_blank"
                    class="cursor-pointer text-lime-700 ml-2"
                    >[klik di sini]</a
                  >
                </div>
              </li>
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  4
                </div>

                <div class="ml-2 p-1">
                  Lihat transaksi zakat anda
                  <Link :href="route('zakat.index')" class="text-lime-700 ml-2"
                    >[klik di sini]</Link
                  >
                </div>
              </li>
            </ol>

            <p class="py-4">
              Untuk keterangan lebih lanjut, hubungi panitia UPZIS:
            </p>
            <ul class="list-none list-inside ml-4">
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  &check;
                </div>
                <div class="ml-2 p-1">
                  melalui WhatsApp
                  <a
                    href="http://bit.ly/sayazakat"
                    target="_blank"
                    class="cursor-pointer text-lime-700 ml-2"
                    >[klik di sini]</a
                  >
                </div>
              </li>
              <li class="flex">
                <div
                  class="rounded-full bg-lime-700 text-gray-100 mt-1 w-5 h-5 text-center text-sm ring-2 ring-yellow-300"
                >
                  &check;
                </div>
                <div class="ml-2 p-1">
                  gerai zakat di Masjid Al Muhajirin, Bukit Pamulang Indah
                </div>
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
