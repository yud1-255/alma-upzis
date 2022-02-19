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
            <div v-if="can.submitForOthers">
              <h2 class="text-l my-4 font-semibold leading-tight text-gray-800">
                Penerimaan oleh Panitia
              </h2>
              <div class="flex">
                <div>
                  <Label>Kepala Keluarga</Label>
                  <autocomplete
                    placeholder="Cari berdasarkan nama"
                    :items="families"
                    key="id"
                    value="head_of_family"
                    isAsync="true"
                    @input="searchFamily"
                    @selected="setFamily"
                  />
                </div>
                <div>
                  <Label for="transaction_no">Terima dari</Label>
                  <Input v-model="form.receive_from_name" />
                </div>
                <div>
                  <Label for="total_rp">Total (Rp)</Label>
                  <InputNumeric
                    v-model="form.total_rp"
                    placeholder="0"
                    class="text-right"
                    readonly
                  />
                </div>
                <div>
                  <Label for="hijri_year">Periode Zakat</Label>
                  <Input
                    v-model="form.hijri_year"
                    class="text-right"
                    readonly
                  />
                </div>
              </div>
            </div>

            <form @submit.prevent="submit">
              <h2
                v-if="!can.submitForOthers"
                class="text-l my-4 font-semibold leading-tight text-gray-800"
              >
                Penerimaan zakat
              </h2>
              <div v-if="!can.submitForOthers" class="flex flex-wrap">
                <div>
                  <Label for="family_head">Kepala Keluarga</Label>
                  <Input v-model="form.family_head" />
                </div>
                <div>
                  <Label for="total_rp">Total (Rp)</Label>
                  <InputNumeric
                    v-model="form.total_rp"
                    placeholder="0"
                    class="text-right"
                    readonly
                  />
                </div>
                <div>
                  <Label for="hijri_year">Periode Zakat</Label>
                  <Input
                    v-model="form.hijri_year"
                    class="text-right"
                    readonly
                  />
                </div>
              </div>
              <div class="overflow-auto">
                <h2
                  class="text-l my-4 font-semibold leading-tight text-gray-800"
                >
                  Keterangan Muzakki
                </h2>
                <div
                  v-for="zakat_line in form.zakat_lines"
                  :key="zakat_line.id"
                >
                  <!-- TODO implement checkbox to disallow at backend -->
                  <div class="flex">muzakki: {{ zakat_line.muzakki_name }}</div>
                  <div class="flex flex-wrap py-2">
                    <InputNumeric
                      v-model="zakat_line.fitrah_rp"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.fitrah_kg"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (kg)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.fitrah_lt"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (lt)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.maal_rp"
                      @change="calculateTotalZakat"
                      placeholder="Maal (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.profesi_rp"
                      @change="calculateTotalZakat"
                      placeholder="Profesi (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.infaq_rp"
                      @change="calculateTotalZakat"
                      placeholder="Infaq (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.wakaf_rp"
                      @change="calculateTotalZakat"
                      placeholder="Wakaf (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.fidyah_rp"
                      @change="calculateTotalZakat"
                      placeholder="Fidyah (Rp)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.fidyah_kg"
                      @change="calculateTotalZakat"
                      placeholder="Fidyah (kg)"
                      class="w-24"
                    />
                    <InputNumeric
                      v-model="zakat_line.kafarat_rp"
                      @change="calculateTotalZakat"
                      placeholder="Kafarat (Rp)"
                      class="w-24"
                    />
                    <!-- TODO implement minus button -->
                  </div>
                </div>
              </div>
              <form @submit.prevent="addMuzakki" class="py-4">
                <div>
                  <h2>Tambah muzakki baru?</h2>
                </div>
                <div class="flex">
                  <Input
                    v-model="muzakkiForm.name"
                    placeholder="Nama"
                    class="w-24"
                  />
                  <Input
                    v-model="muzakkiForm.phone"
                    placeholder="Telepon"
                    class="w-24"
                  />

                  <button class="text-green-700">Tambah Muzakki</button>
                </div>
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
import InputNumeric from "@/Components/InputNumeric.vue";
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
    InputNumeric,
    Checkbox,
    Head,
  },
  setup(props) {
    const form = useForm({
      transaction_no: props.transaction_no,
      transaction_date: new Date().toISOString().split("T")[0],
      hijri_year: 1443,
      family_head: props.family.head_of_family,
      receive_from_name: props.family.head_of_family,
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
      phone: "",
      family_id: props.family.id,
      address: props.family.address,
      is_bpi: props.family.is_bpi,
      bpi_block_no: props.family.bpi_block_no,
      bpi_house_no: props.family.bpi_house_no,
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
      this.muzakkiForm.post(route("muzakki.store"), {
        preserveScroll: true,
        onSuccess: () => {
          this.muzakkiForm.reset("name", "phone");
        },
      });
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
