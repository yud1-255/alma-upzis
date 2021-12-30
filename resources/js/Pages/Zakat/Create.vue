<template>
  <Head title="Buat zakat baru"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Buat Transaksi Zakat
      </h1>
    </template>

    <!-- TODO modify styling for Create -->

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <BreezeValidationErrors class="mb-4" />
            <form @submit.prevent="submit">
              <div>
                <Label for="transaction_no">transaction_no</Label>
                <Input v-model="form.transaction_no" />
              </div>
              <div>
                <Label for="transaction_date">transaction_date</Label>
                <Input v-model="form.transaction_date" />
              </div>
              <div>
                <Label for="family_head">family_head</Label>
                <Input v-model="form.family_head" />
              </div>
              <div>
                <Label for="total_rp">total_rp</Label>
                <Input v-model="form.total_rp" />
              </div>
              <div>
                <Label for="hijri_year">hijri_year</Label>
                <Input v-model="form.hijri_year" />
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
                  <div class="flex">muzakki: {{ zakat_line.muzakki_name }}</div>
                  <div class="flex">
                    <Input
                      v-model="zakat_line.fitrah_rp"
                      placeholder="fitrah_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.fitrah_kg"
                      placeholder="fitrah_kg"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.fitrah_lt"
                      placeholder="fitrah_lt"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.maal_rp"
                      placeholder="maal_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.profesi_rp"
                      placeholder="profesi_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.infaq_rp"
                      placeholder="infaq_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.wakaf_rp"
                      placeholder="wakaf_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.fidyah_rp"
                      placeholder="fidyah_rp"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.fidyah_kg"
                      placeholder="fidyah_kg"
                      class="w-16"
                    />
                    <Input
                      v-model="zakat_line.kafarat_rp"
                      placeholder="kafarat_rp"
                      class="w-16"
                    />
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
import Label from "@/Components/Label.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Label,
    Input,
    Head,
  },
  setup(props) {
    const form = useForm({
      transaction_no: "UPZ/1443/01",
      // receive_from: null,
      // zakat_pic: null,
      transaction_date: "2021-10-10",
      hijri_year: 1443,
      family_head: "Prasetyo",
      total_rp: 999000,
      zakat_lines: [],
    });

    props.muzakkis.forEach((muzakki) => {
      form.zakat_lines.push({
        muzakki_id: muzakki.id,
        muzakki_name: muzakki.name,
        fitrah_rp: null,
        fitrah_kg: null,
        fitrah_lt: null,
        maal_rp: null,
        profesi_rp: null,
        infaq_rp: null,
        wakaf_rp: null,
        fidyah_kg: null,
        fidyah_rp: null,
        kafarat_rp: null,
      });
    });

    return { form };
  },
  props: {
    errors: null,
    muzakkis: Array,
  },
  methods: {
    submit() {
      this.form.zakat_lines.forEach((item) => {
        item.fitrah_rp = item.fitrah_rp ?? 0;
        item.fitrah_kg = item.fitrah_kg ?? 0;
        item.fitrah_lt = item.fitrah_lt ?? 0;
        item.maal_rp = item.maal_rp ?? 0;
        item.profesi_rp = item.profesi_rp ?? 0;
        item.infaq_rp = item.infaq_rp ?? 0;
        item.wakaf_rp = item.wakaf_rp ?? 0;
        item.fidyah_kg = item.fidyah_kg ?? 0;
        item.fidyah_rp = item.fidyah_rp ?? 0;
        item.kafarat_rp = item.kafarat_rp ?? 0;
      });
      this.form.post(route("zakat.store"));
    },
  },
};
</script>
