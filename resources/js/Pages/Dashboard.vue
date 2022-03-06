<template>
  <Head title="Dashboard" />

  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="my-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1>Assalamualaikum, Muzakki</h1>

            <p class="py-2 mt-4">
              Selamat datang di aplikasi zakat UPZIS Al Munawwarah/Masjid Al
              Muhajirin. Proses zakat anda dalam empat langkah mudah:
            </p>

            <ol class="list-decimal list-inside ml-4">
              <li>
                Lengkapi data keluarga
                <Link :href="route('family.create')" class="text-green-600 ml-2"
                  >[link]</Link
                >
              </li>
              <li>
                Mulai pengisian zakat
                <Link :href="route('zakat.create')" class="text-green-600 ml-2"
                  >[link]</Link
                >
              </li>
              <li>
                Transfer dan konfirmasi pembayaran zakat
                <a
                  href="http://bit.ly/sayazakat"
                  target="_blank"
                  class="cursor-pointer text-green-600 ml-2"
                  >[link:wa]</a
                >
              </li>
              <li>
                Lihat transaksi zakat anda
                <Link :href="route('zakat.index')" class="text-green-600 ml-2"
                  >[link]</Link
                >
              </li>
            </ol>

            <p class="py-2">
              Untuk keterangan lebih lanjut, hubungi panitia UPZIS:
            </p>
            <ul class="list-disc list-inside ml-4">
              <li>
                melalui WhatsApp:
                <a
                  href="http://bit.ly/sayazakat"
                  target="_blank"
                  class="cursor-pointer text-green-600 ml-2"
                  >[klik di sini]</a
                >
              </li>
              <li>gerai zakat di Masjid Al Muhajirin, Bukit Pamulang Indah</li>
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
