<template>
  <label
    :for="label"
    class="block text-sm font-medium text-gray-700"
  >{{ label }}</label>
  <div
    ref="inputField"
    class="mt-1"
  >
    <input
      :id="label"
      v-model="query"
      type="text"
      :name="label"
      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
      :placeholder="placeholder"
      @click="toggle"
      @keyup.delete="handleBackspace"
    >
  </div>
  <div
    v-if="open"
    class="fixed inset-0 transition-opacity"
  >
    <div
      class="absolute inset-0 bg-transparent"
      @click="toggle"
    />
  </div>
  <transition
    enter-active-class="duration-150 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="duration-100 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <teleport to="#destination">
      <div
        v-show="open"
        ref="listboxCollabsible"
        class="absolute z-20 mt-1 w-full rounded-md bg-white shadow-lg"
      >
        <ul
          tabindex="-1"
          role="listbox"
          aria-labelledby="listbox-label"
          class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
          <!--
                      Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.

                      Highlighted: "text-white bg-indigo-600", Not Highlighted: "text-gray-900"
                      -->
          <li
            v-for="(suggestion, index) in suggestions"
            :id="'listbox-item-' + index"
            :key="suggestion.value"
            role="option"
            class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-8 pr-4"
            @click="select(suggestion)"
          >
            <!--  Selected: "font-semibold", Not Selected: "font-normal" -->
            <span :class="[(selected && selected.id === suggestion.id) ? 'font-semibold' : 'font-normal', 'block truncate']">
              {{ suggestion.name }}
            </span>

            <!--
                          Checkmark, only display for selected option.

                          Highlighted: "text-white", Not Highlighted: "text-indigo-600"
                          &ndash;&gt;-->
            <span
              v-show="(selected && selected.id === suggestion.id)"
              class="absolute inset-y-0 left-0 flex items-center pl-1.5"
            >
              <!--          Heroicon name: check -->
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </li>
        </ul>
      </div>
    </teleport>
  </transition>
</template>

<script>
import {createPopper} from "@popperjs/core";

export default {
  name: "Autosuggest",
  props: {
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
  emits: ['selected', 'selectedObject'],
  data() {
    return {
      search_result: [],
      selected: null,
      query: '',
      suggestions: [],
      open: false,
    }
  },
  watch: {
    query(query, oldQuery) {

      if (query === undefined) {
        return;
      }

      /*if (query === '')
        this.$emit('selected', null)*/

      // In case of a select, the query gets updated, we need to prevent the suggestions from showing again.
      if(query === _.get(this.selected, 'name')){
        return;
      }

      if(query.length <=2)
        return;

      let self = this

      return axios.get(this.$route(this.route, {search: query}))
          .then((result) => {
            self.suggestions = result.data

            // if previously the suggestions were not shown toggle them
            if(!this.open)
              this.toggle()
          } /*console.log(result.data)*/)
    }
  },
  methods: {
    select(suggestion) {
      this.selected = suggestion
      this.query = _.get(suggestion, 'name')
      this.open = false
      this.$emit('selected', suggestion.id)
      this.$emit('selectedObject', suggestion)
    },
    toggle() {

      if(this.suggestions.length > 0)
        this.open = !this.open

      if(this.open)
        new createPopper(this.$refs.inputField, this.$refs.listboxCollabsible, {
          placement: 'bottom-start',
          modifiers: [
            {
              name: "sameWidth",
              enabled: true,
              phase: "beforeWrite",
              requires: ["computeStyles"],
              fn: ({state}) => {
                state.styles.popper.width = `${state.rects.reference.width}px`;
              },
              effect: ({state}) => {
                state.elements.popper.style.width = `${
                  state.elements.reference.offsetWidth
                }px`;
              },
            },
            {
              name: 'offset',
              options: {
                offset: [0, 8],
              },
            },
          ]
        })

    },
    handleBackspace() {

      if(this.query.length > 2)
        return;

      this.open = false
      this.suggestions = []
      this.selected = null
      this.$emit('selected', null)
    }
  }

}
</script>

<style scoped>

</style>
