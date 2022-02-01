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
            <Label>family_search</Label>
            <autocomplete
              :placeholder="family_placeholder"
              :items="families"
              key="id"
              value="head_of_family"
              isAsync="true"
              @input="searchFamily"
              @selected="setFamily"
            />
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
                  <!-- TODO implement checkbox to disallow at backend -->
                  <div class="flex">muzakki: {{ zakat_line.muzakki_name }}</div>
                  <div class="flex flex-wrap py-2">
                    <Input
                      v-model="zakat_line.fitrah_rp"
                      @change="calculateTotalZakat"
                      placeholder="fitrah_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.fitrah_kg"
                      placeholder="fitrah_kg"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.fitrah_lt"
                      placeholder="fitrah_lt"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.maal_rp"
                      @change="calculateTotalZakat"
                      placeholder="maal_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.profesi_rp"
                      @change="calculateTotalZakat"
                      placeholder="profesi_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.infaq_rp"
                      @change="calculateTotalZakat"
                      placeholder="infaq_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.wakaf_rp"
                      @change="calculateTotalZakat"
                      placeholder="wakaf_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.fidyah_rp"
                      @change="calculateTotalZakat"
                      placeholder="fidyah_rp"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.fidyah_kg"
                      placeholder="fidyah_kg"
                      class="w-24"
                    />
                    <Input
                      v-model="zakat_line.kafarat_rp"
                      @change="calculateTotalZakat"
                      placeholder="kafarat_rp"
                      class="w-24"
                    />
                    <!-- TODO implement minus button -->
                  </div>
                </div>
              </div>
              <form @submit.prevent="addMuzakki">
                <div>
                  <h2>Muzakki baru</h2>
                </div>
                <div class="flex">
                  <Input
                    v-model="muzakkiForm.name"
                    placeholder="name"
                    class="w-24"
                  />
                  <div>
                    <input type="checkbox" v-model="muzakkiForm.is_bpi" />
                    is_bpi
                  </div>
                  <Input
                    v-model="muzakkiForm.address"
                    placeholder="address"
                    class="w-24"
                  />
                  <Input
                    v-model="muzakkiForm.bpi_block_no"
                    placeholder="bpi_block_no"
                    class="w-24"
                  />
                  <Input
                    v-model="muzakkiForm.bpi_house_no"
                    placeholder="bpi_house_no"
                    class="w-24"
                  />
                </div>
                <button class="px-6 py-2 text-white bg-gray-900 rounded">
                  Tambah Muzakki
                </button>
              </form>
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
import { onMounted, onUpdated } from "vue";

import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Autocomplete from "@/Components/Autocomplete.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Label from "@/Components/Label.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Autocomplete,
    Link,
    Label,
    Input,
    Checkbox,
    Head,
  },
  setup(props) {
    const form = useForm({
      transaction_no: props.transaction_no,
      transaction_date: new Date().toISOString().split("T")[0],
      hijri_year: 1443,
      family_head: props.family.head_of_family,
      total_rp: 0,
      zakat_lines: [],
    });

    const refreshMuzakki = () => {
      form.zakat_lines = [];
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
    };

    onMounted(() => refreshMuzakki());
    onUpdated(() => refreshMuzakki());

    const muzakkiForm = useForm({
      name: "",
      family_id: props.family.id,
      address: "",
      is_bpi: true,
      bpi_block_no: "",
      bpi_house_no: "",
    });

    return { form, muzakkiForm };
  },
  data() {
    return {
      families: [],
    };
  },
  props: {
    errors: null,
    transaction_no: String,
    family: Object,
    family_placeholder: String,
    muzakkis: Array,
    can: Object,
  },
  methods: {
    calculateTotalZakat() {
      let totalRp = this.form.zakat_lines.reduce((total, line) => {
        total =
          total +
          Number(line.fitrah_rp) +
          Number(line.maal_rp) +
          Number(line.profesi_rp) +
          Number(line.infaq_rp) +
          Number(line.wakaf_rp) +
          Number(line.fidyah_rp) +
          Number(line.kafarat_rp);
        return total;
      }, 0);

      this.form.total_rp = totalRp;
    },
    searchFamily(searchTerm) {
      axios
        .get(route("family.search", { search: searchTerm }), {
          search: searchTerm,
        })
        .then((res) => {
          this.families = res.data;
        });
    },
    setFamily(result) {
      let familyId = result.id;
      this.$inertia.get(
        route("zakat.create", { familyId: familyId }),
        {},
        {
          preserveScroll: true,
        }
      );
    },
    addMuzakki() {
      this.muzakkiForm.post(route("muzakki.store"), { preserveScroll: true });
    },
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
