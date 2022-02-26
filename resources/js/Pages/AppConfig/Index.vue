<template>
  <Head title="Pengaturan"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Pengaturan Aplikasi</h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <table class="align-top">
              <tr v-for="(appConfig, idx) in appConfigs" :key="appConfig.key">
                <th
                  v-if="
                    appConfig.key == 'fitrah_amount' &&
                    appConfigs[idx - 1].key != 'fitrah_amount'
                  "
                  class="align-top text-left"
                  rowspan="3"
                >
                  {{ appConfig.key }}
                </th>
                <th
                  v-else-if="appConfig.key != 'fitrah_amount'"
                  class="text-left"
                >
                  {{ appConfig.key }}
                </th>
                <td>
                  <Input v-model="appConfig.value" class="w-96"></Input>
                </td>
                <td>
                  <Link class="text-red-700" @click="saveConfig(appConfig)"
                    >Ubah</Link
                  >
                </td>
              </tr>
            </table>
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
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { Link } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Link,
    Input,
    Button,
    Head,
  },
  setup() {
    const appConfigForm = useForm({
      id: "",
      key: "",
      value: "",
    });

    return { appConfigForm };
  },
  props: {
    appConfigs: Array,
  },
  computed: {
    uniqueKeys() {
      return [...new Set(this.appConfigs.map((a) => a.key))];
    },
  },
  methods: {
    keyItems(key) {
      return this.appConfigs.filter((a) => a.key == key);
    },
    saveConfig(appConfig) {
      this.appConfigForm.id = appConfig.id;
      this.appConfigForm.key = appConfig.key;
      this.appConfigForm.value = appConfig.value;

      this.appConfigForm.put(route("app_config.update", appConfig.id), {
        preserveScroll: true,
        onSuccess: () => {
          console.log("success");
        },
      });
    },
  },
};
</script>
