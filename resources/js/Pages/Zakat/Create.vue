<template>
  <Head title="Buat zakat baru"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Buat Transaksi Zakat
      </h1>
    </template>

    <!-- TODO implement view for Create -->

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <BreezeValidationErrors class="mb-4" />
            <form @submit.prevent="submit">
              <div>
                <label for="transaction_no">transaction_no</label>
                <input
                  type="text"
                  v-model="form.transaction_no"
                  class="
                    w-full
                    px-4
                    py-2
                    mt-2
                    border
                    rounded-md
                    focus:outline-none focus:ring-1 focus:ring-blue-600
                  "
                />
              </div>
              <div>
                <label for="transaction_date">transaction_date</label>
                <input
                  type="text"
                  v-model="form.transaction_date"
                  class="
                    w-full
                    px-4
                    py-2
                    mt-2
                    border
                    rounded-md
                    focus:outline-none focus:ring-1 focus:ring-blue-600
                  "
                />
              </div>
              <div>
                <label for="family_head">family_head</label>
                <input
                  type="text"
                  v-model="form.family_head"
                  class="
                    w-full
                    px-4
                    py-2
                    mt-2
                    border
                    rounded-md
                    focus:outline-none focus:ring-1 focus:ring-blue-600
                  "
                />
              </div>
              <div>
                <label for="total_rp">total_rp</label>
                <input
                  type="text"
                  v-model="form.total_rp"
                  class="
                    w-full
                    px-4
                    py-2
                    mt-2
                    border
                    rounded-md
                    focus:outline-none focus:ring-1 focus:ring-blue-600
                  "
                />
              </div>
              <div>
                <label for="hijri_year">hijri_year</label>
                <input
                  type="text"
                  v-model="form.hijri_year"
                  class="
                    w-full
                    px-4
                    py-2
                    mt-2
                    border
                    rounded-md
                    focus:outline-none focus:ring-1 focus:ring-blue-600
                  "
                />
              </div>
              <div class="overflow-auto">
                <h2
                  class="text-l my-4 font-semibold leading-tight text-gray-800"
                >
                  Jumlah Muzakki
                </h2>
                <div
                  v-for="zakat_line in form.zakat_lines"
                  :key="zakat_line.id"
                >
                  <div class="flex">mid: {{ zakat_line.muzakki_id }}</div>
                  <div class="flex">
                    <Input v-model="zakat_line.fitrah_rp" />
                    <Input v-model="zakat_line.fitrah_kg" />
                    <Input v-model="zakat_line.fitrah_lt" />
                    <Input v-model="zakat_line.maal_rp" />
                    <Input v-model="zakat_line.profesi_rp" />
                    <Input v-model="zakat_line.infaq_rp" />
                    <Input v-model="zakat_line.wakaf_rp" />
                    <Input v-model="zakat_line.fidyah_rp" />
                    <Input v-model="zakat_line.fidyah_kg" />
                    <Input v-model="zakat_line.kafarat_rp" />
                  </div>
                </div>
              </div>
              <div class="flex items-center mt-4">
                <button class="px-6 py-2 text-white bg-gray-900 rounded">
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Input from "@/Components/Input.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Input,
    Head,
  },
  setup() {
    const form = useForm({
      transaction_no: "UPZ/1443/01",
      // receive_from: null,
      // zakat_pic: null,
      transaction_date: "2021-10-10",
      hijri_year: 1443,
      family_head: "Prasetyo",
      total_rp: 999000,
      zakat_lines: [
        {
          muzakki_id: 1,
          fitrah_rp: 1100,
          fitrah_kg: 1200,
          fitrah_lt: 1,
          maal_rp: 1300,
          profesi_rp: 1400,
          infaq_rp: 1500,
          wakaf_rp: 1600,
          fidyah_kg: 2,
          fidyah_rp: 900,
          kafarat_rp: 800,
        },
        {
          muzakki_id: 2,
          fitrah_rp: 1000,
          fitrah_kg: 200,
          fitrah_lt: 1,
          maal_rp: 300,
          profesi_rp: 400,
          infaq_rp: 500,
          wakaf_rp: 600,
          fidyah_kg: 2,
          fidyah_rp: 100,
          kafarat_rp: 110,
        },
      ],
    });

    return { form };
  },
  props: {
    errors: null,
  },
  methods: {
    submit() {
      this.form.post(route("zakat.store"));
    },
  },
};
</script>
