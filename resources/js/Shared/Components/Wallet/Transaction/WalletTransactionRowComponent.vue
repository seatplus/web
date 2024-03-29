<template>
  <li
    :class="even ? 'bg-gray-50' : 'bg-white'"
    class="grid grid-cols-2 sm:grid-cols-12 sm:gap-x-0 sm:gap-y-1 grid-flow-row text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Date
      </label>
      <Time :timestamp="entry.date" />
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-2 truncate">
      <span class="sr-only">Brought or Sold</span>
      {{ entry.is_buy ? 'Bought' : 'Sold' }}
    </div>
    <div class="px-6 py-4 sm:py-1 self-center truncate col-span-1 sm:col-span-5">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Type
      </label>
      <div
        v-if="entry.type"
        class="flex items-center"
      >
        <div class="shrink-0">
          <EveImage
            :object="entry.type"
            :size="256"
            tailwind_class="h-12 w-12 rounded-full"
          />
        </div>
        <div class="ml-4">
          <h3 class="leading-6 font-medium text-gray-900">
            {{ entry.type.name }}
          </h3>
          <p
            v-if="entry.type.group || entry.type.category"
            class="text-sm text-gray-500 truncate"
          >
            {{ getTypeDescription }}
          </p>
        </div>
      </div>
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center truncate col-span-1 sm:col-span-2 text-right">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Total
      </label>
      {{ getTotal() }}
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center truncate text-right col-span-2 sm:col-span-1">
      <span class="sr-only">Expand</span>
      <button
        type="button"
        :class="[expanded ? 'text-white bg-gray-500 hover:bg-gray-600': 'text-gray-500 hover:text-gray-600','inline-flex items-center p-1 border border-transparent rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500', 'transition ease-in-out duration-200']"
        @click="toggle"
      >
        <svg
          :class="['h-5 w-5 transform transition ease-in-out duration-200', expanded ? 'rotate-180' : 'rotate-0']"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </div>
    <ExtendedWalletTransactionRowComponent
      v-if="expanded"
      class="col-span-2 sm:col-span-12 px-6 sm:px-3 py-4 sm:py-1"
      :entry="entry"
    />
  </li>
</template>

<script>
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ExtendedWalletTransactionRowComponent from "./ExtendedWalletTransactionRowComponent.vue";

export default {
    name: "WalletTransactionRowComponent",
    components: {ExtendedWalletTransactionRowComponent, Time, EveImage },
    props: {
        entry: {
            required: true
        },
        even: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            expanded: false
        }
    },
    computed: {
        getTypeDescription() {

            let group_name = _.get(this.entry.type, 'group.name')
            let category_name = _.get(this.entry.type, 'entry.type.category.name')

            if (_.isUndefined(group_name))
                return ''

            if(_.isUndefined(category_name))
                return group_name

            return group_name + ' | ' + category_name
        }
    },
    methods: {
        toggle() {
            this.expanded = !this.expanded
        },
        getTotal() {
            let total = this.entry.quantity * this.entry.unit_price

            return total.toLocaleString() ?? ''
        }
    }
}
</script>

<style scoped>

</style>
