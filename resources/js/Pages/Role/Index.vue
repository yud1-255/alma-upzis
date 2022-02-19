<template>
  <Head title="Zakat"></Head>
  <BreezeAuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">
        Pengaturan Pengguna
      </h1>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <table>
              <thead class="font-bold bg-gray-300 border-b-2">
                <td class="px-4 py-2">ID</td>
                <td class="px-4 py-2">Nama</td>
                <td class="px-4 py-2">Email</td>
                <td class="px-4 py-2">Role</td>
                <td class="px-4 py-2"></td>
              </thead>
              <tr v-for="user in users.data" :key="user.id">
                <td class="px-4 py-2">{{ user.id }}</td>
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2">
                  <select @change="setSelectedRole($event, user)">
                    <option
                      v-for="role in roles"
                      v-bind:key="role.id"
                      v-bind:value="role.id"
                      :selected="user.roles[0]?.id == role.id"
                    >
                      {{ role.name }}
                    </option>
                  </select>
                </td>
                <td class="px-4 py-2">
                  <Link
                    class="text-red-700"
                    @click="submitUserRole(user.id, user.selectedRoleId)"
                  >
                    Ubah
                  </Link>
                </td>
              </tr>
            </table>
            <pagination :links="users.links" />
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
import Pagination from "@/Components/Pagination.vue";
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
    Pagination,
  },
  setup() {
    const roleForm = useForm({
      user_id: null,
      role_id: null,
    });

    return { roleForm };
  },
  props: {
    errors: null,
    users: Object,
    roles: Array,
  },
  methods: {
    setSelectedRole(event, user) {
      let roleId = event.target.value;
      user.selectedRoleId = roleId;
    },
    submitUserRole(userId, roleId) {
      this.roleForm.user_id = userId;
      this.roleForm.role_id = roleId;

      this.$inertia.post(route("roles.assign"), this.roleForm, {
        preserveState: true,
        preserveScroll: true,
      });
    },
  },
};
</script>
