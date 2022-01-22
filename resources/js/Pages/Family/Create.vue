<template>
  <Head title="Daftar muzakki"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Daftarkan muzakki
      </h1>
    </template>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            Hello!
            <BreezeValidationErrors class="mb-4" />
            <form @submit.prevent="createFamily()" class="md:flex">
              <div>
                <Label for="head_of_family">head_of_family</Label>
                <Input v-model="familyForm.head_of_family" />
              </div>
              <div>
                <Label for="phone">phone</Label>
                <Input v-model="familyForm.phone" />
              </div>
              <div>
                <Label for="address">address</Label>
                <Input v-model="familyForm.address" />
              </div>
              <div class="flex items-center mt-4">
                <button class="px-6 py-2 text-white bg-gray-900 rounded">
                  Lanjut
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
      head_of_family: props.family?.head_of_family,
      phone: props.family?.phone,
      address: props.family?.address,
    });

    const muzakkiForm = useForm({
      name: "",
      family_id: props.family?.id,
      address: "",
      is_bpi: true,
      bpi_block_no: "",
      bpi_house_no: "",
    });

    return { familyForm, muzakkiForm };
  },
  props: {
    errors: null,
    family: Object,
    muzakkis: Array,
  },
  methods: {
    createFamily() {
      // TODO submit family form
      this.familyForm.post(route("family.store"), { preserveScroll: true });
    },
    addMuzakki() {
      this.muzakkiForm.post(route("muzakki.store"), { preserveScroll: true });
    },
  },
};
</script>
