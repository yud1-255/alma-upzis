<template>
  <div>
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white border-b-2 border-yellow-200 print:hidden">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <!-- <BreezeApplicationLogo class="block h-9 w-auto" /> -->
                  <img src="/assets/yam-logo.png" class="h-12" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <BreezeNavLink
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                >
                  Beranda
                </BreezeNavLink>
                <BreezeNavLink
                  :href="route('family.create')"
                  :active="route().current('family.create')"
                >
                  Muzakki
                </BreezeNavLink>
                <div
                  class="cursor-pointer inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                >
                  <BreezeDropdown align="left">
                    <template #trigger>
                      <button class="inline-flex">
                        Zakat

                        <icon-dropdown />
                      </button>
                    </template>
                    <template #content>
                      <BreezeDropdownLink :href="route('zakat.create')"
                        >Buat zakat baru</BreezeDropdownLink
                      ><BreezeDropdownLink
                        v-if="!isAdministrator && !isUpzis"
                        :href="route('zakat.index')"
                        >Transaksi zakat saya</BreezeDropdownLink
                      >
                      <BreezeDropdownLink
                        v-if="isAdministrator || isUpzis"
                        :href="route('zakat.index')"
                        >Transaksi zakat</BreezeDropdownLink
                      >
                    </template>
                  </BreezeDropdown>
                </div>

                <!-- TODO refactor into dropdown menu component -->
                <div
                  class="cursor-pointer inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                >
                  <BreezeDropdown
                    align="left"
                    v-if="isAdministrator || isUpzis"
                  >
                    <template #trigger>
                      <button class="inline-flex">
                        Laporan

                        <icon-dropdown />
                      </button>
                    </template>
                    <template #content>
                      <BreezeDropdownLink :href="route('zakat.muzakkiRecap')"
                        >Rekap berdasarkan muzakki</BreezeDropdownLink
                      ><BreezeDropdownLink :href="route('zakat.dailyRecap')"
                        >Rekap penerimaan harian</BreezeDropdownLink
                      ><BreezeDropdownLink :href="route('zakat.onlinePayments')"
                        >Pembayaran online</BreezeDropdownLink
                      ><BreezeDropdownLink :href="route('zakat.muzakkiList')"
                        >Daftar muzakki</BreezeDropdownLink
                      >
                    </template>
                  </BreezeDropdown>
                </div>

                <div
                  class="cursor-pointer inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                >
                  <BreezeDropdown align="left" v-if="isAdministrator">
                    <template #trigger>
                      <button class="inline-flex">
                        Pengaturan

                        <icon-dropdown />
                      </button>
                    </template>
                    <template #content>
                      <BreezeDropdownLink
                        :href="route('roles.index', { all: 1 })"
                        >Pengguna</BreezeDropdownLink
                      ><BreezeDropdownLink :href="route('app_config.index')"
                        >Konfigurasi Aplikasi</BreezeDropdownLink
                      >
                    </template>
                  </BreezeDropdown>
                </div>
              </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <!-- Settings Dropdown -->
              <div class="ml-3 relative">
                <BreezeDropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                      >
                        {{ $page.props.auth.user.name }}

                        <icon-dropdown />
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <BreezeDropdownLink
                      :href="route('logout')"
                      method="post"
                      as="button"
                    >
                      Log Out
                    </BreezeDropdownLink>
                  </template>
                </BreezeDropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
              >
                <svg
                  class="h-6 w-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden print:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <BreezeResponsiveNavLink
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              Beranda
            </BreezeResponsiveNavLink>

            <BreezeResponsiveNavLink :href="route('zakat.index')">
              Zakat
            </BreezeResponsiveNavLink>
            <BreezeResponsiveNavLink
              class="pl-6"
              :href="route('zakat.index')"
              :active="route().current('zakat.index')"
            >
              Transaksi saya
            </BreezeResponsiveNavLink>
            <BreezeResponsiveNavLink
              class="pl-6"
              :href="route('zakat.create')"
              :active="route().current('zakat.create')"
            >
              Buat transaksi
            </BreezeResponsiveNavLink>
            <BreezeResponsiveNavLink :href="route('family.create')">
              Muzakki
            </BreezeResponsiveNavLink>
            <BreezeResponsiveNavLink
              class="pl-6"
              :href="route('family.create')"
              :active="route().current('family.create')"
            >
              Set data muzakki
            </BreezeResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
              <div class="font-medium text-base text-gray-800">
                {{ $page.props.auth.user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">
                {{ $page.props.auth.user.email }}
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <BreezeResponsiveNavLink
                :href="route('logout')"
                method="post"
                as="button"
              >
                Log Out
              </BreezeResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow print:hidden" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>

      <!-- Page Footer -->
      <footer class="bg-white shadow h-full" v-if="$slots.footer">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="footer" />
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
import BreezeApplicationLogo from "@/Components/ApplicationLogo.vue";
import BreezeDropdown from "@/Components/Dropdown.vue";
import BreezeDropdownLink from "@/Components/DropdownLink.vue";
import BreezeNavLink from "@/Components/NavLink.vue";
import BreezeResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import IconDropdown from "@/Components/IconDropdown.vue";

import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    BreezeApplicationLogo,
    BreezeDropdown,
    BreezeDropdownLink,
    BreezeNavLink,
    BreezeResponsiveNavLink,
    Link,
    IconDropdown,
  },

  data() {
    return {
      showingNavigationDropdown: false,
    };
  },

  computed: {
    isAdministrator() {
      return (
        this.$page.props.auth.user.roles.find(
          (role) => role.name == "administrator"
        ) != null
      );
    },
    isUpzis() {
      return (
        this.$page.props.auth.user.roles.find((role) => role.name == "upzis") !=
        null
      );
    },
  },
};
</script>
