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
              <tr v-for="(appConfig, idx) in appConfigs" :key="appConfig.key + '-' + idx">
                <th
                  v-if="
                    appConfig.key == 'fitrah_amount' &&
                    appConfigs[idx - 1].key != 'fitrah_amount'
                  "
                  class="align-top text-left"
                  rowspan="3"
                >
                  {{ configTranslation[appConfig.key] ?? appConfig.key }}
                </th>
                <th
                  v-else-if="appConfig.key != 'fitrah_amount'"
                  class="text-left"
                >
                  {{ configTranslation[appConfig.key] ?? appConfig.key }}
                  <template v-if="appConfig.key === 'hijri_year'">
                    <br />
                    <span class="text-xs font-normal text-gray-500">
                      Auto-detect: {{ autoDetectHijriYear }}
                    </span>
                  </template>
                </th>
                <td>
                  <template v-if="appConfig.key === 'hijri_year'">
                    <div class="flex items-center gap-2">
                      <label class="flex items-center gap-1 text-sm text-gray-600">
                        <input
                          type="checkbox"
                          v-model="hijriOverrideEnabled"
                          class="rounded border-gray-300"
                        />
                        Override
                      </label>
                      <Input
                        v-model="appConfig.value"
                        class="w-96"
                        :disabled="!hijriOverrideEnabled"
                        :placeholder="hijriOverrideEnabled ? '' : autoDetectHijriYear"
                        maxlength="4"
                        pattern="[0-9]{4}"
                      ></Input>
                    </div>
                  </template>
                  <template v-else>
                    <Input v-model="appConfig.value" class="w-96"></Input>
                  </template>
                </td>
                <td>
                  <Link
                    v-if="appConfig.key !== 'hijri_year' || hijriOverrideEnabled"
                    class="text-red-700"
                    @click="saveConfig(appConfig)"
                    >Ubah</Link
                  >
                  <Link
                    v-else-if="appConfig.key === 'hijri_year' && appConfig.value"
                    class="text-gray-500"
                    @click="clearHijriOverride(appConfig)"
                    >Hapus Override</Link
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
  props: {
    appConfigs: Array,
    autoDetectHijriYear: String,
  },
  setup() {
    const appConfigForm = useForm({
      id: "",
      key: "",
      value: "",
    });

    return { appConfigForm };
  },
  data() {
    const hijriConfig = this.appConfigs?.find((c) => c.key === "hijri_year");
    return {
      hijriOverrideEnabled: !!(hijriConfig && hijriConfig.value),
      configTranslation: {
        hijri_year: "Tahun Hijriyah (Saat ini)",
        hijri_year_beginning: "Tahun Hijriyah (Awal Entri Data)",
        fitrah_amount: "Pilihan Zakat Fitrah (Rp)",
        bank_account: "Rekening Panitia",
        confirmation_phone: "Kontak Panitia (WhatsApp)",
        remove_qr_start_date: "Mulai nonaktifkan display QR",
        remove_qr_end_date: "Akhir nonaktifkan display QR",
        remove_transfer_start_date: "Mulai nonaktifkan transfer rekening",
        remove_transfer_end_date: "Akhir nonaktifkan transfer rekening",
        check_kk_limit: "Jumlah maksimal cek Kartu Keluarga",
      },
    };
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
    clearHijriOverride(appConfig) {
      appConfig.value = "";
      this.saveConfig(appConfig);
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
