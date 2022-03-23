<template>
  <div class="autocomplete">
    <Input
      v-model="search"
      @input="onChange"
      :placeholder="placeholder"
      class="w-full md:w-auto"
    ></Input>
  </div>
  <ul v-show="isOpen" class="shadow-lg">
    <li v-if="isLoading" class="loading text-sm">loading ...</li>
    <li
      v-else
      v-for="result in results"
      :key="result[key]"
      @click="setResult(result)"
      class="cursor-pointer hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100 text-sm"
    >
      {{ result[value] }}
    </li>
    <li v-if="results.length == 0">
      <slot name="empty" />
    </li>
  </ul>
</template>

<script>
import Input from "@/Components/Input.vue";

export default {
  name: "Autocomplete",
  props: {
    items: Array,
    key: "",
    value: "",
    isAsync: false,
    placeholder: "",
  },
  data() {
    return {
      search: "",
      results: [],
      isLoading: false,
      isOpen: false,
    };
  },
  watch: {
    items: function (value, oldValue) {
      if (this.isAsync) {
        this.results = value;
        this.isOpen = true;
        this.isLoading = false;
      }
    },
  },
  components: {
    Input,
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
  },
  destroyed() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  emits: {
    input: null,
    selected: null,
  },
  methods: {
    setResult(result) {
      this.$emit("selected", result);

      this.search = result[this.value];
      this.isOpen = false;
    },
    filterResults() {
      this.results = this.items.filter(
        (item) =>
          item[this.value].toLowerCase().indexOf(this.search.toLowerCase()) > -1
      );
    },
    reset() {
      this.search = "";
    },
    onChange() {
      this.$emit("input", this.search);
      if (this.isAsync) {
        this.isLoading = true;
      } else {
        this.filterResults();
        this.isOpen = true;
      }
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.isOpen = false;
      }
    },
  },
  setup() {},
};
</script>
<style>
.autocomplete-result.is-active,
.autocomplete-result:hover {
  background-color: #4aae9b;
  color: white;
}
</style>
