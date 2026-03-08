<template>
  <Head title="Pengajuan Zakat Sederhana"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1>Pengajuan Zakat Sederhana</h1>
    </template>

    <div class="py-2 md:py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-4 md:p-6 bg-white border-b border-gray-200">
            <ValidationErrors />

            <form @submit.prevent="submit" @keypress.enter.prevent>
              <!-- Contact Section -->
              <h2>Data Kepala Keluarga</h2>
              <div class="md:flex md:flex-wrap md:gap-x-4">
                <div class="py-1">
                  <Label>Nama Lengkap <span class="text-red-500">*</span></Label>
                  <Input
                    v-model="form.head_of_family"
                    class="w-full md:w-auto"
                    placeholder="Nama lengkap kepala keluarga"
                  />
                  <InputError
                    :message="form.errors.head_of_family || clientErrors.head_of_family"
                  />
                </div>
                <div class="py-1">
                  <Label>Email <span class="text-red-500">*</span></Label>
                  <Input
                    v-model="form.email"
                    type="email"
                    class="w-full md:w-auto"
                    placeholder="email@contoh.com"
                  />
                  <InputError
                    :message="form.errors.email || clientErrors.email"
                  />
                </div>
                <div class="py-1">
                  <Label>Nomor Telepon <span class="text-red-500">*</span></Label>
                  <Input
                    v-model="form.phone"
                    class="w-full md:w-auto"
                    placeholder="081234567890"
                  />
                  <InputError
                    :message="form.errors.phone || clientErrors.phone"
                  />
                </div>
              </div>

              <div class="py-2 mt-2">
                <Label>Periode Zakat</Label>
                <Input :modelValue="hijri_year" class="w-full md:w-auto" readonly />
              </div>

              <!-- Fitrah Amount Selector -->
              <div v-if="fitrah_amount && fitrah_amount.length > 0" class="py-2">
                <Label>Besaran zakat fitrah</Label>
                <span class="mr-3">Rp</span>
                <select v-model="defaultFitrahAmount" @change="applyFitrahAmount">
                  <option value="">-- Pilih --</option>
                  <option
                    v-for="(amount, idx) in fitrah_amount"
                    :key="idx"
                    :value="amount"
                  >
                    {{ Number(amount).toLocaleString("id") }}
                  </option>
                </select>
              </div>

              <!-- Member List Section -->
              <h2 class="mt-4">Daftar Anggota Keluarga</h2>

              <div
                v-for="(member, memberIndex) in form.members"
                :key="memberIndex"
                class="border rounded-lg p-3 md:p-4 mb-4"
                :class="member.muzakki_id != null ? 'bg-gray-50' : 'bg-yellow-50'"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-sm">
                      {{ memberIndex === 0 ? 'Kepala Keluarga' : `Anggota #${memberIndex}` }}
                    </span>
                    <span
                      v-if="member.muzakki_id == null && memberIndex > 0"
                      class="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded"
                    >
                      baru
                    </span>
                  </div>
                  <span
                    v-if="memberIndex > 0"
                    class="cursor-pointer text-red-600 flex items-center text-xs hover:text-red-800"
                    title="Hapus anggota"
                    @click="removeMember(memberIndex)"
                  >
                    <span class="hidden md:inline mr-1">Hapus</span>
                    <TrashIcon class="h-4 w-4" />
                  </span>
                </div>

                <!-- Member Name -->
                <div class="mb-2">
                  <Label>Nama Anggota <span class="text-red-500">*</span></Label>
                  <Input
                    v-model="member.name"
                    class="w-full md:w-auto"
                    placeholder="Nama anggota keluarga"
                  />
                  <InputError
                    :message="form.errors[`members.${memberIndex}.name`] || clientErrors[`members.${memberIndex}.name`]"
                  />
                </div>

                <!-- Zakat Amount Fields -->
                <div class="flex flex-wrap gap-2">
                  <div
                    v-for="zakatType in zakatTypes"
                    :key="zakatType.key"
                    class="w-full sm:w-auto"
                  >
                    <Label class="text-xs">{{ zakatType.label }}</Label>
                    <InputNumeric
                      v-model="member.zakat[zakatType.key]"
                      :placeholder="zakatType.placeholder"
                      class="w-full sm:w-28 text-right"
                    />
                    <InputError
                      :message="form.errors[`members.${memberIndex}.zakat.${zakatType.key}`]"
                    />
                  </div>
                </div>
              </div>

              <!-- Add Member Button -->
              <div class="py-2">
                <button
                  type="button"
                  class="flex items-center text-green-700 hover:text-green-900 text-sm"
                  @click="addMember"
                >
                  <UserAddIcon class="h-5 mr-1" />
                  <span>Tambah Anggota</span>
                </button>
              </div>

              <!-- Validation Summary -->
              <InputError :message="clientErrors.members_amount" />

              <!-- Submit Button -->
              <div class="flex items-center mt-4">
                <Button :disabled="form.processing">
                  {{ form.processing ? 'Mengirim...' : 'Ajukan' }}
                </Button>
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
import ValidationErrors from "@/Components/ValidationErrors.vue";
import Input from "@/Components/Input.vue";
import InputNumeric from "@/Components/InputNumeric.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";

import { Head } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";

import { TrashIcon } from "@heroicons/vue/solid";
import { UserAddIcon } from "@heroicons/vue/solid";

const ZAKAT_TYPES = [
  { key: "fitrah_rp", label: "Zakat Fitrah (Rp)", placeholder: "0" },
  { key: "fitrah_kg", label: "Zakat Fitrah (Kg)", placeholder: "0" },
  { key: "fitrah_lt", label: "Zakat Fitrah (Lt)", placeholder: "0" },
  { key: "maal_rp", label: "Zakat Maal (Rp)", placeholder: "0" },
  { key: "profesi_rp", label: "Zakat Profesi (Rp)", placeholder: "0" },
  { key: "infaq_rp", label: "Infaq (Rp)", placeholder: "0" },
  { key: "wakaf_rp", label: "Wakaf (Rp)", placeholder: "0" },
  { key: "fidyah_rp", label: "Fidyah (Rp)", placeholder: "0" },
  { key: "fidyah_kg", label: "Fidyah (Kg)", placeholder: "0" },
  { key: "kafarat_rp", label: "Kafarat (Rp)", placeholder: "0" },
];

function createEmptyZakat() {
  return {
    fitrah_rp: null,
    fitrah_kg: null,
    fitrah_lt: null,
    maal_rp: null,
    profesi_rp: null,
    infaq_rp: null,
    wakaf_rp: null,
    fidyah_rp: null,
    fidyah_kg: null,
    kafarat_rp: null,
  };
}

function createMemberRow(muzakkiId, name) {
  return {
    muzakki_id: muzakkiId,
    name: name,
    zakat: createEmptyZakat(),
  };
}

export default {
  components: {
    BreezeAuthenticatedLayout,
    ValidationErrors,
    Input,
    InputNumeric,
    InputError,
    Label,
    Button,
    Head,
    TrashIcon,
    UserAddIcon,
  },
  setup(props) {
    const members = [];

    // First row is always the head of family
    if (props.muzakkis && props.muzakkis.length > 0) {
      props.muzakkis.forEach((muzakki) => {
        members.push(createMemberRow(muzakki.id, muzakki.name));
      });
    } else {
      // No existing muzakkis — create a single row for the head of family
      members.push(createMemberRow(null, props.prefill.head_of_family || ""));
    }

    const form = useForm({
      head_of_family: props.prefill.head_of_family || "",
      email: props.prefill.email || "",
      phone: props.prefill.phone || "",
      members: members,
    });

    return { form };
  },
  props: {
    errors: { type: Object, default: () => ({}) },
    prefill: { type: Object, default: () => ({}) },
    muzakkis: { type: Array, default: () => [] },
    hijri_year: { type: String, default: "" },
    fitrah_amount: { type: Array, default: () => [] },
  },
  data() {
    return {
      zakatTypes: ZAKAT_TYPES,
      defaultFitrahAmount: "",
      clientErrors: {},
    };
  },
  methods: {
    addMember() {
      this.form.members.push(createMemberRow(null, ""));
    },

    removeMember(index) {
      if (index === 0) return;
      this.form.members.splice(index, 1);
    },

    applyFitrahAmount() {
      if (!this.defaultFitrahAmount) return;

      this.form.members.forEach((member) => {
        member.zakat.fitrah_rp = this.defaultFitrahAmount;
      });
    },

    /**
     * Check if a member row has at least one non-zero zakat amount.
     * Used to decide whether to include the row in submission
     * and whether to require a name.
     */
    memberHasAmount(member) {
      return Object.values(member.zakat).some(
        (val) => val !== null && val !== "" && Number(val) > 0
      );
    },

    validate() {
      const errors = {};

      // Contact fields
      if (!this.form.head_of_family || !this.form.head_of_family.trim()) {
        errors.head_of_family = "Nama lengkap wajib diisi.";
      }

      if (!this.form.email || !this.form.email.trim()) {
        errors.email = "Email wajib diisi.";
      } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(this.form.email.trim())) {
          errors.email = "Format email tidak valid.";
        }
      }

      if (!this.form.phone || !this.form.phone.trim()) {
        errors.phone = "Nomor telepon wajib diisi.";
      }

      // Member name validation — only required if the member has non-zero amounts
      let hasAnyAmount = false;
      this.form.members.forEach((member, index) => {
        const hasAmount = this.memberHasAmount(member);
        if (hasAmount) {
          hasAnyAmount = true;
          if (!member.name || !member.name.trim()) {
            errors[`members.${index}.name`] =
              "Nama anggota wajib diisi jika ada jumlah zakat.";
          }
        }
      });

      // At least one non-zero amount across all members
      if (!hasAnyAmount) {
        errors.members_amount =
          "Minimal satu jumlah zakat harus diisi (lebih dari 0).";
      }

      this.clientErrors = errors;
      return Object.keys(errors).length === 0;
    },

    /**
     * Prepare form data for submission.
     * Normalizes null/empty zakat values to 0 and filters out
     * members with all-zero amounts.
     */
    prepareFormData() {
      this.form.members.forEach((member) => {
        ZAKAT_TYPES.forEach((type) => {
          const val = member.zakat[type.key];
          if (val === null || val === "" || isNaN(val)) {
            member.zakat[type.key] = 0;
          } else {
            member.zakat[type.key] = Number(val);
          }
        });
      });
    },

    submit() {
      if (!this.validate()) return;

      this.prepareFormData();

      // Filter out members with all-zero amounts before submission
      const originalMembers = this.form.members;
      const activeMembers = this.form.members.filter((member) =>
        this.memberHasAmount(member)
      );

      // Temporarily replace members with only active ones for the POST
      this.form.members = activeMembers;

      this.form.post(route("simple-zakat.store"), {
        preserveScroll: true,
        onError: () => {
          // Restore all members so the user can still edit
          this.form.members = originalMembers;
        },
      });
    },
  },
};
</script>
