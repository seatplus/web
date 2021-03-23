<template>
  <div>
    <Autosuggest
      :key="uniqueID"
      :route="route"
      :label="label"
      :placeholder="placeholder"
      @selectedObject="onSelected"
    />

    <span
      v-for="selection in selections"
      :key="selection.id"
      class="inline-flex items-center py-0.5 pl-2 pr-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700"
    >
      {{ selection.name }}
      <button
        type="button"
        class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white"
        @click="removeEntry(selection.id)"
      >
        <span class="sr-only">Remove {{ selection.name }}</span>
        <svg
          class="h-2 w-2"
          stroke="currentColor"
          fill="none"
          viewBox="0 0 8 8"
        >
          <path
            stroke-linecap="round"
            stroke-width="1.5"
            d="M1 1l6 6m0-6L1 7"
          />
        </svg>
      </button>
    </span>
  </div>
</template>

<script>
// TODO import { VueAutosuggest } from "vue-autosuggest"
import Autosuggest from "@/Shared/Components/Autosuggest";
export default {
  name: "Multiselect",
  components: {
    Autosuggest
    /*VueAutosuggest*/},
  props: {
    modelValue: {
      required: true
    },
    route: {
      required: true,
      type: String
    },
    label: {
      required: true,
      type: String
    },
    placeholder: {
      required: true,
      type: String
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      uniqueID: +new Date(),
      selections: this.modelValue,
      suggestions: [],
    }
  },
  methods: {
    onSelected(selection) {

      this.selections.push(selection)

      this.update()
    },
    removeEntry(id) {

      this.selections = this.selections.filter(suggestion => suggestion.id !== id)

      this.update()
    },
    update() {
      let ids = _.map( this.selections, (object) => object.id)

      this.$emit('update:modelValue', ids)
      this.uniqueID++
    }
  }
}
</script>

<style scoped>

</style>
