<template>
  <form @submit.prevent="submit()">
    <h2>Kepala Keluarga</h2>
    <div class="md:flex">
      <div class="py-1">
        <Label for="head_of_family">Nama</Label>
        <Input v-model="familyForm.head_of_family" class="w-full md:w-auto" />
      </div>
      <div class="py-1">
        <Label for="phone">Telepon</Label>
        <Input v-model="familyForm.phone" class="w-full md:w-auto" />
      </div>
    </div>
    <div>
      <h2>Alamat</h2>
      <div class="flex space-x-2">
        <input
          type="radio"
          id="is_bpi_true"
          class="text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
          v-model="familyForm.is_bpi"
          :value="1"
          @change="setAddress"
        />
        <Label for="is_bpi_true" class="cursor-pointer">Warga BPI</Label>
        <input
          type="radio"
          id="is_bpi_false"
          class="text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
          v-model="familyForm.is_bpi"
          :value="0"
          @change="setAddress"
        />
        <Label for="is_bpi_false" class="cursor-pointer">Luar BPI</Label>
      </div>

      <!-- TODO refactor into select component (Vue) -->
      <div v-if="familyForm.is_bpi" id="bpi_address" class="my-2">
        <Label>Blok/Nomor</Label>
        <select v-model="selectedBlock" @change="setAddress">
          <option></option>
          <option v-for="(_, block) in blockNumbers" :key="block">
            {{ block }}
          </option>
        </select>
        <select v-model="selectedBlockNumber" @change="setAddress">
          <option></option>
          <option
            v-for="blockNumber in blockNumbers[selectedBlock]"
            :key="blockNumber"
          >
            {{ blockNumber }}
          </option>
        </select>
        <select v-model="selectedHouseNumber" @change="setAddress" class="ml-4">
          <option></option>
          <option v-for="houseNumber in houseNumbers" :key="houseNumber">
            {{ houseNumber }}
          </option>
        </select>
      </div>

      <div id="ext-address">
        <div class="flex mt-4">
          <Label for="address">Alamat Lengkap</Label>
          <Label v-if="familyForm.is_bpi" class="mx-1">(auto-generated)</Label>
        </div>
        <Input v-model="familyForm.address" class="w-80" />
      </div>
    </div>
    <div class="flex items-center mt-4">
      <button
        class="px-6 py-2 text-green-100 bg-lime-700 rounded w-full md:w-auto"
      >
        Lanjut
      </button>
    </div>
  </form>
</template>
<script>
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: { Label, Input },

  setup() {
    const familyForm = useForm({
      head_of_family: "",
      phone: "",
      kk_number: "",
      address: "",
      is_bpi: 0,
      bpi_block_no: "",
      bpi_house_no: "",
    });

    return { familyForm };
  },
  props: {
    blockNumbers: Object,
    houseNumbers: Array,
  },
  emits: {
    submit: null,
  },
  data() {
    return {
      selectedBlock: "",
      selectedBlockNumber: "",
      selectedHouseNumber: "",
    };
  },
  methods: {
    setAddress() {
      if (this.familyForm.is_bpi) {
        this.familyForm.bpi_block_no = `${this.selectedBlock}${this.selectedBlockNumber}`;
        this.familyForm.bpi_house_no = this.selectedHouseNumber;

        if (
          !!this.selectedBlock &&
          !!this.selectedBlockNumber &&
          !!this.selectedHouseNumber
        ) {
          this.familyForm.address = `Bukit Pamulang Indah ${this.familyForm.bpi_block_no} no ${this.familyForm.bpi_house_no}`;
        } else {
          this.familyForm.address = "";
        }
      } else {
        this.selectedBlock = "";
        this.selectedBlockNumber = "";
        this.selectedHouseNumber = "";

        this.familyForm.bpi_block_no = "";
        this.familyForm.bpi_house_no = "";
      }
    },
    submit() {
      this.$emit("submit", this.familyForm);
    },
  },
};
</script>
