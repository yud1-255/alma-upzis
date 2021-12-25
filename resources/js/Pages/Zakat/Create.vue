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
import { Head } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Head,
  },
  setup() {
    const form = useForm({
      transaction_no: null,
      // receive_from: null,
      // zakat_pic: null,
      transaction_date: null,
      hijri_year: null,
      family_head: null,
      total_rp: null,
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
