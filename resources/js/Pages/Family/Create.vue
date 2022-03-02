<template>
  <Head title="Daftar muzakki"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Daftarkan muzakki</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <BreezeValidationErrors class="mb-4" />
            <form @submit.prevent="createFamily()">
              <h2>Kepala Keluarga</h2>
              <div class="flex">
                <div>
                  <Label for="head_of_family">Nama</Label>
                  <Input v-model="familyForm.head_of_family" />
                </div>
                <div>
                  <Label for="phone">Telepon</Label>
                  <Input v-model="familyForm.phone" />
                </div>
              </div>
              <div>
                <h2>Alamat</h2>
                <div class="flex space-x-2">
                  <input
                    type="radio"
                    id="is_bpi_true"
                    class="text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="familyForm.is_bpi"
                    :value="1"
                    @change="setAddress"
                  />
                  <Label for="is_bpi_true" class="cursor-pointer"
                    >Warga BPI</Label
                  >
                  <input
                    type="radio"
                    id="is_bpi_false"
                    class="text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="familyForm.is_bpi"
                    :value="0"
                    @change="setAddress"
                  />
                  <Label for="is_bpi_false" class="cursor-pointer"
                    >Luar BPI</Label
                  >
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
                  <select
                    v-model="selectedHouseNumber"
                    @change="setAddress"
                    class="ml-4"
                  >
                    <option></option>
                    <option
                      v-for="houseNumber in houseNumbers"
                      :key="houseNumber"
                    >
                      {{ houseNumber }}
                    </option>
                  </select>
                </div>

                <div id="ext-address">
                  <div class="flex mt-4">
                    <Label for="address">Alamat Lengkap</Label>
                    <Label v-if="familyForm.is_bpi" class="mx-1"
                      >(auto-generated)</Label
                    >
                  </div>
                  <Input v-model="familyForm.address" class="w-80" />
                </div>
              </div>
              <div v-if="family == null" class="flex items-center mt-4">
                <button class="px-6 py-2 text-white bg-gray-900 rounded">
                  Lanjut
                </button>
              </div>
            </form>

            <div v-if="family != null">
              <div>
                <h2 class="mt-6">Muzakki dalam keluarga</h2>
                <form @submit.prevent="addMuzakki">
                  <table>
                    <thead class="font-bold border-b-2">
                      <td>Nama</td>
                      <td>Telepon</td>
                      <td>Alamat</td>
                      <td></td>
                    </thead>
                    <tbody>
                      <tr v-for="muzakki in muzakkis" :key="muzakki.id">
                        <td class="py-2">{{ muzakki.name }}</td>
                        <td class="py-2">{{ muzakki.phone }}</td>
                        <td class="py-2">{{ muzakki.address }}</td>
                        <td>
                          <Link
                            @click="deleteMuzakki(muzakki.id)"
                            class="text-red-700"
                          >
                            Hapus
                          </Link>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td>
                          <Input
                            v-model="muzakkiForm.name"
                            placeholder="Nama"
                            class="w-24"
                          />
                        </td>
                        <td>
                          <Input
                            v-model="muzakkiForm.phone"
                            placeholder="Telepon"
                            class="w-24"
                          />
                        </td>
                        <td>
                          <div v-bind:class="{ invisible: useFamilyAddress }">
                            <Input
                              v-model="muzakkiForm.address"
                              placeholder="Alamat"
                              class="w-80"
                            />
                          </div>
                        </td>
                        <td>
                          <button class="text-green-700">Tambah Muzakki</button>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div class="flex my-2 space-x-2 align-top">
                            <Checkbox
                              id="use_same_address"
                              v-model="useFamilyAddress"
                              :checked="useFamilyAddress"
                            />
                            <Label for="use_same_address" class="cursor-pointer"
                              >gunakan alamat keluarga</Label
                            >
                          </div>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </form>

                <div v-if="family != null" class="flex items-center mt-4">
                  <button
                    @click="updateFamily"
                    class="px-6 py-2 text-white bg-gray-900 rounded"
                  >
                    Simpan
                  </button>
                </div>
              </div>
            </div>
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
import Checkbox from "@/Components/Checkbox.vue";
import Label from "@/Components/Label.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeValidationErrors,
    Link,
    Label,
    Input,
    Checkbox,
    Head,
  },
  setup(props) {
    const familyForm = useForm({
      id: props.family?.id,
      head_of_family: props.family?.head_of_family,
      phone: props.family?.phone,
      address: props.family?.address,
      is_bpi: props.family?.is_bpi,
      bpi_block_no: props.family?.bpi_block_no,
      bpi_house_no: props.family?.bpi_house_no,
    });

    const muzakkiForm = useForm({
      name: "",
      phone: "",
      family_id: props.family?.id,
      address: props.family?.address,
      is_bpi: props.family?.is_bpi,
      bpi_block_no: props.family?.bpi_block_no,
      bpi_house_no: props.family?.bpi_house_no,
    });

    return { familyForm, muzakkiForm };
  },
  mounted() {
    this.selectedBlock = this.family?.bpi_block_no?.charAt(0);
    this.selectedBlockNumber = this.family?.bpi_block_no?.substring(1);
    this.selectedHouseNumber = this.family?.bpi_house_no;
  },
  props: {
    errors: null,
    family: Object,
    muzakkis: Array,
    blockNumbers: Object,
    houseNumbers: Array,
  },
  data() {
    return {
      selectedBlock: "",
      selectedBlockNumber: "",
      selectedHouseNumber: "",
      useFamilyAddress: true,
    };
  },
  watch: {
    family: function (value, oldValue) {
      this.familyForm.id = value.id;

      this.muzakkiForm.family_id = value.id;
      this.muzakkiForm.address = value.address;
      this.muzakkiForm.is_bpi = value.is_bpi;
      this.muzakkiForm.bpi_block_no = value.bpi_block_no;
      this.muzakkiForm.bpi_house_no = value.bpi_house_no;
    },
    useFamilyAddress: function (value, oldValue) {
      if (value) {
        this.muzakkiForm.address = this.family?.address;
        this.muzakkiForm.is_bpi = this.family?.is_bpi;
        this.muzakkiForm.bpi_block_no = this.family?.bpi_block_no;
        this.muzakkiForm.bpi_house_no = this.family?.bpi_house_no;
      } else {
        this.muzakkiForm.address = "";
        this.muzakkiForm.is_bpi = false;
        this.muzakkiForm.bpi_block_no = "";
        this.muzakkiForm.bpi_house_no = "";
      }
    },
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
    createFamily() {
      if (this.familyForm.id == null) {
        this.familyForm.post(route("family.store"), { preserveScroll: true });
      } else {
        this.familyForm.put(
          route("family.update", { family: this.familyForm }),
          { preserveScroll: true }
        );
      }
    },
    updateFamily() {
      this.familyForm.put(route("family.update", { family: this.familyForm }), {
        preserveScroll: true,
        onSuccess: () => {
          this.$inertia.visit(route("dashboard"));
        },
      });
    },
    addMuzakki() {
      this.muzakkiForm.post(route("muzakki.store"), {
        preserveScroll: true,
        onSuccess: () => {
          this.muzakkiForm.reset("name", "phone", "address");
          this.useFamilyAddress = true;
        },
        onError: () => {
          window.scrollTo({ top: 0, left: 0, behavior: "smooth" });
        },
      });
    },
    deleteMuzakki(id) {
      this.$inertia.delete(route("muzakki.destroy", id), {
        preserveScroll: true,
      });
    },
  },
};
</script>
