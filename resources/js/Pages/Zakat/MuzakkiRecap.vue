<template>
  <Head title="Rekap Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Rekap Pendapatan Zakat</h1>
      <div class="py-6 bg-white border-b border-gray-200">
        <table>
          <thead class="font-bold bg-gray-300 border-b-2">
            <td class="px-4 py-2">No. Zakat</td>
            <td class="px-4 py-2">Muzakki</td>
            <td class="px-4 py-2">Fitrah (rp kg lt)</td>
            <td class="px-4 py-2">Maal</td>
            <td class="px-4 py-2">Profesi</td>
            <td class="px-4 py-2">Infaq Shadaqah</td>
            <td class="px-4 py-2">Fidyah (rp kg)</td>
            <td class="px-4 py-2">Wakaf</td>
            <td class="px-4 py-2">Kafarat</td>
            <td class="px-4 py-2">Tanggal</td>
            <td class="px-4 py-2">Terima dari</td>
            <td class="px-4 py-2">Petugas</td>
          </thead>
          <tbody>
            <tr v-for="zakat_line in zakats.data" :key="zakat_line.id">
              <td>
                <Link :href="route('zakat.show', zakat_line.zakat_id)">{{
                  zakat_line.transaction_no
                }}</Link>
              </td>
              <td>{{ zakat_line.muzakki_name }}</td>
              <td class="text-right">
                {{ Number(zakat_line.fitrah_rp).toLocaleString("id") }} |
                {{ Number(zakat_line.fitrah_kg).toLocaleString("id") }} |
                {{ Number(zakat_line.fitrah_lt).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.maal_rp).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.profesi_rp).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.infaq_rp).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.fidyah_rp).toLocaleString("id") }} |
                {{ Number(zakat_line.fidyah_kg).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.wakaf_rp).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ Number(zakat_line.kafarat_rp).toLocaleString("id") }}
              </td>
              <td class="text-right">
                {{ zakat_line.transaction_date }}
              </td>
              <td class="text-right">{{ zakat_line.receive_from_name }}</td>
              <td class="text-right">{{ zakat_line.zakat_pic_name }}</td>
            </tr>
          </tbody>
        </table>
        <pagination :links="zakats.links" />
      </div>
    </template>
  </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link,
    Pagination,
  },
  props: {
    zakats: Object,
  },
};
</script>
