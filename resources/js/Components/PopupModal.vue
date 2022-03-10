<template>
  <transition name="fade">
    <div
      class="bg-gray-600 bg-opacity-50 fixed z-1 inset-x-0 inset-y-0 flex"
      v-if="isVisible"
      @click="this.isVisible = false"
    >
      <div
        class="bg-white shadow m-auto p-4 max-w-2xl min-h-min rounded-lg"
        @click.stop=""
      >
        <slot></slot>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  name: "PopupModal",
  data() {
    return {
      isVisible: false,
    };
  },
  methods: {
    open() {
      this.isVisible = true;
    },
    close() {
      this.isVisible = false;
    },
  },
};
</script>
<style scoped>
/* css class for the transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.popup-modal {
  background-color: rgba(0, 0, 0, 0.5);
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  z-index: 1;
}

.window {
  background: #fff;
  border-radius: 5px;
  box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
  padding: 1rem;
}
</style>
