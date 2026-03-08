<template>
  <div class="w-full">
    <!-- Divider -->
    <div class="flex items-center my-6">
      <div class="flex-grow border-t border-gray-300"></div>
      <span class="mx-4 text-sm text-gray-500">atau</span>
      <div class="flex-grow border-t border-gray-300"></div>
    </div>

    <!-- Social Login Buttons -->
    <div class="space-y-3">
      <!-- Google -->
      <a
        :href="googleRedirectUrl"
        class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-sm hover:bg-gray-50 transition ease-in-out duration-150"
      >
        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
        </svg>
        Masuk dengan Google
      </a>

      <!-- Facebook -->
      <a
        :href="facebookRedirectUrl"
        class="flex items-center justify-center w-full px-4 py-3 border border-transparent rounded-lg text-white font-medium text-sm hover:opacity-90 transition ease-in-out duration-150"
        style="background-color: #1877F2;"
      >
        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
          <path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
        </svg>
        Masuk dengan Facebook
      </a>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    googleRedirectUrl() {
      return "/auth/google/redirect";
    },
    facebookRedirectUrl() {
      return "/auth/facebook/redirect";
    },
  },
};
</script>
