<template>
  <Head title="Buat zakat baru"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Buat Transaksi Zakat</h1>
    </template>

    <div class="py-2 md:py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <ErrorModal ref="errorModal"></ErrorModal>
            <div v-if="can.submitForOthers">
              <div
                class="flex space-x-2"
                :class="{ 'opacity-50': is_family_requested }"
              >
                <input
                  type="radio"
                  id="is_submit_for_other_true"
                  class="text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
                  v-model="isSubmitAsUpzis"
                  :value="true"
                  :disabled="is_family_requested"
                  @change="setAddress"
                />
                <Label for="is_submit_for_other_true" class="cursor-pointer"
                  >Gerai</Label
                >
                <input
                  type="radio"
                  id="is_submit_for_other_false"
                  class="text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
                  v-model="isSubmitAsUpzis"
                  :value="false"
                  :disabled="is_family_requested"
                />
                <Label for="is_submit_for_other_false" class="cursor-pointer"
                  >Online</Label
                >
              </div>
              <div v-if="isSubmitAsUpzis">
                <h2>Penerimaan oleh Panitia</h2>
                <div class="md:flex">
                  <div class="py-1">
                    <Label>Kepala Keluarga</Label>
                    <Autocomplete
                      ref="autocomplete"
                      placeholder="Cari berdasarkan nama"
                      :items="families"
                      key="id"
                      value="head_of_family"
                      isAsync="true"
                      @input="searchFamily"
                      @selected="setFamily"
                    >
                      <template #empty>
                        <span
                          class="cursor-pointer flex text-sm"
                          @click="showFamilyForm"
                        >
                          <PlusSmIcon class="h-4 my-1" />
                          <span class="py-1">Daftarkan keluarga baru</span>
                        </span>
                      </template>
                    </Autocomplete>
                  </div>
                  <div class="py-1">
                    <Label for="transaction_no">Terima dari</Label>
                    <Input
                      v-model="form.receive_from_name"
                      class="w-full md:w-auto"
                    />
                  </div>
                  <div class="py-1">
                    <Label for="total_rp">Total (Rp)</Label>
                    <InputNumeric
                      v-model="form.total_rp"
                      placeholder="0"
                      class="text-right w-full md:w-auto"
                      readonly
                    />
                  </div>
                  <div class="py-1">
                    <Label for="hijri_year">Periode Zakat</Label>
                    <Input
                      v-model="form.hijri_year"
                      class="text-right w-full md:w-auto"
                      readonly
                    />
                  </div>
                </div>
              </div>
            </div>

            <form @submit.prevent="submit">
              <h2 v-if="!can.submitForOthers || !isSubmitAsUpzis">
                Penerimaan zakat
              </h2>
              <div
                v-if="!can.submitForOthers || !isSubmitAsUpzis"
                class="md:flex md:flex-wrap"
              >
                <div class="py-1">
                  <Label for="family_head">Kepala Keluarga</Label>
                  <Input v-model="form.family_head" class="w-full md:w-auto" />
                </div>
                <div class="py-1">
                  <Label for="total_rp">Total (Rp)</Label>
                  <InputNumeric
                    v-model="form.total_rp"
                    placeholder="0"
                    class="text-right w-full md:w-auto"
                    readonly
                  />
                </div>
                <div class="py-1 hidden md:inline-block">
                  <Label for="hijri_year">Periode Zakat</Label>
                  <Input
                    v-model="form.hijri_year"
                    class="text-right w-full md:w-auto"
                    readonly
                  />
                </div>
              </div>
              <div class="py-2">
                <Label>Besaran zakat fitrah</Label>
                <span class="mr-3">Rp</span>
                <select v-model="defaultFitrahAmount" @change="setFitrahAmount">
                  <option
                    v-for="(amount, idx) in fitrah_amount"
                    :key="idx"
                    :value="amount"
                  >
                    {{ Number(amount).toLocaleString("id") }}
                  </option>
                </select>
              </div>
              <div class="overflow-auto">
                <h2>Keterangan Muzakki</h2>
                <div
                  v-for="zakat_line in form.zakat_lines"
                  :key="zakat_line.id"
                >
                  <div class="flex">
                    <div>muzakki: {{ zakat_line.muzakki_name }}</div>
                    <span
                      class="cursor-pointer ml-4 text-red-600 flex text-xs"
                      title="Hapus"
                      @click="removeZakatLine(zakat_line)"
                    >
                      Hapus
                      <UserRemoveIcon class="h-5 mx-1" />
                    </span>
                  </div>
                  <div class="flex flex-wrap py-2">
                    <InputNumeric
                      v-model="zakat_line.fitrah_rp"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.fitrah_kg"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (kg)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.fitrah_lt"
                      @change="calculateTotalZakat"
                      placeholder="Fitrah (lt)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.maal_rp"
                      @change="calculateTotalZakat"
                      placeholder="Maal (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.profesi_rp"
                      @change="calculateTotalZakat"
                      placeholder="Profesi (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.infaq_rp"
                      @change="calculateTotalZakat"
                      placeholder="Infaq (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.wakaf_rp"
                      @change="calculateTotalZakat"
                      placeholder="Wakaf (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.fidyah_rp"
                      @change="calculateTotalZakat"
                      placeholder="Fidyah (Rp)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.fidyah_kg"
                      @change="calculateTotalZakat"
                      placeholder="Fidyah (kg)"
                      class="w-24 m-1"
                    />
                    <InputNumeric
                      v-model="zakat_line.kafarat_rp"
                      @change="calculateTotalZakat"
                      placeholder="Kafarat (Rp)"
                      class="w-24 m-1"
                    />
                  </div>
                </div>
              </div>
              <form @submit.prevent="addMuzakki" class="py-4">
                <div>
                  <h2>Tambah muzakki baru?</h2>
                </div>
                <div v-if="removedMuzakkiCount == 0" class="flex">
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
                  <button
                    class="text-green-700 text-xs flex"
                    title="Tambah muzakki"
                  >
                    Tambah
                    <UserAddIcon class="h-5 mx-1" />
                  </button>
                </div>
                <div v-else>
                  <span class="italic mr-4"
                    >{{ removedMuzakkiCount }} muzakki tidak diikutkan</span
                  >
                  <span
                    class="cursor-pointer text-green-600"
                    @click="resetMuzakkiForm"
                    >Tampilkan kembali</span
                  >
                </div>
              </form>
              <div class="flex items-center mt-4">
                <button
                  class="px-6 py-2 text-green-100 bg-lime-700 rounded w-full md:w-auto"
                >
                  Simpan
                </button>
              </div>
            </form>
            <PopupModal ref="popup">
              <FamilyForm
                :blockNumbers="blockNumbers"
                :houseNumbers="houseNumbers"
                @submit="createFamily"
              ></FamilyForm>
            </PopupModal>
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
import Button from "@/Components/Button.vue";
import PopupModal from "@/Components/PopupModal.vue";
import ErrorModal from "@/Components/ErrorModal.vue";

import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

import { UserRemoveIcon } from "@heroicons/vue/solid";
import { UserAddIcon } from "@heroicons/vue/solid";
import { PlusSmIcon } from "@heroicons/vue/solid";

import FamilyForm from "@/Components/Domain/FamilyForm.vue";

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
    Button,
    Head,
    ErrorModal,
    PopupModal,
    FamilyForm,
    UserRemoveIcon,
    UserAddIcon,
    PlusSmIcon,
  },
  setup(props) {
    const form = useForm({
      transaction_no: props.transaction_no,
      transaction_date: new Date().toISOString().split("T")[0],
      hijri_year: props.hijri_year,
      family_head: props.family.head_of_family,
      receive_from_name: props.family.head_of_family,
      total_rp: 0,
      zakat_lines: [],
      is_submit_as_upzis: props.can.submitForOthers,
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

    return { form, muzakkiForm, refreshMuzakki };
  },
  data() {
    return {
      families: [],
      defaultFitrahAmount: "",
      removedMuzakkiCount: 0,
      isSubmitAsUpzis: this.can.submitForOthers,
    };
  },
  watch: {
    isSubmitAsUpzis(value) {
      this.form.is_submit_as_upzis = this.can.submitForOthers && value;
    },
  },
  props: {
    errors: null,
    transaction_no: String,
    family: Object,
    family_placeholder: String,
    muzakkis: Object,
    hijri_year: String,
    fitrah_amount: Array,
    is_family_requested: Boolean,
    blockNumbers: Object,
    houseNumbers: Array,
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
    setFitrahAmount() {
      this.form.zakat_lines.forEach((line) => {
        line.fitrah_rp = this.defaultFitrahAmount;
      });
      this.calculateTotalZakat();
    },
    addMuzakki() {
      this.muzakkiForm.post(route("muzakki.store"), {
        preserveScroll: true,
        onSuccess: () => {
          this.muzakkiForm.reset("name", "phone");
        },
        onError: () => {
          this.showErrorModal();
        },
      });
    },
    removeZakatLine(zakatLine) {
      this.form.zakat_lines = this.form.zakat_lines.filter(
        (line) => line.muzakki_id != zakatLine.muzakki_id
      );
      this.removedMuzakkiCount++;
      this.calculateTotalZakat();
    },
    resetMuzakkiForm() {
      this.defaultFitrahAmount = "";
      this.removedMuzakkiCount = 0;
      this.refreshMuzakki();
      this.calculateTotalZakat();
    },
    showFamilyForm() {
      this.$refs.autocomplete.reset();
      this.$refs.popup.open();
    },
    createFamily(familyForm) {
      familyForm.post(route("family.register"), {
        onSuccess: () => {
          this.setFamily(this.family);
        },
        onError: () => {
          this.showErrorModal();
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
      this.form.post(route("zakat.store"), {
        onError: () => {
          this.removedMuzakkiCount = 0;
          this.$refs.errorModal.show({
            errors: this.errors,
          });
        },
      });
    },
    showErrorModal() {
      this.$refs.errorModal.show({
        errors: this.errors,
      });
    },
  },
};
</script>
