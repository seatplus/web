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
        <div class="flex-shrink-0">
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
<!--    <Fragment>
        <TableRow :class="even ? 'bg-gray-50' : 'bg-white'">
            <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                <Time :timestamp="entry.date"></Time>
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                {{ entry.is_buy ? 'Bought' : 'Sold' }}
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-normal overflow-hidden overflow-clip text-right text-sm text-gray-500">
                <div v-if="entry.type" class="flex items-center">
                    <div class="flex-shrink-0">
                        <EveImage :object="entry.type" :size="256" tailwind_class="h-12 w-12 rounded-full"/>
                    </div>
                    <div class="ml-4">
                        <h3 class="leading-6 font-medium text-gray-900">
                            {{  entry.type.name }}
                        </h3>
                        <p v-if="entry.type.group || entry.type.category" class="text-sm text-gray-500 truncate">
                            {{ getTypeDescription }}
                        </p>
                    </div>
                </div>
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-normal text-right text-sm text-gray-500">
                {{ getTotal() }}
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-normal text-right text-sm text-gray-500">
                <button type="button" @click="toggle"
                        :class="[expanded ? 'text-white bg-gray-500 hover:bg-gray-600': 'text-gray-500 hover:text-gray-600','inline-flex items-center p-1 border border-transparent rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500', 'transition ease-in-out duration-200']">

                    <svg :class="['h-5 w-5 transform transition ease-in-out duration-200', expanded ? 'rotate-180' : 'rotate-0']" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>

                </button>
            </DataCell>
        </TableRow>
        <TableRow v-if="expanded" :class="even ? 'bg-gray-50' : 'bg-white'">
            <DataCell colspan="5" class="px-6 pb-4 whitespace-nowrap text-sm text-gray-500">
                <ExtendedWalletTransactionRowComponent :entry="entry" />
            </DataCell>
        </TableRow>
    </Fragment>-->
</template>

<script>
import TableRow from "../../../Layout/Cards/Table/TableRow";
import DataCell from "../../../Layout/Cards/Table/DataCell";
/*TODO import {Fragment} from "vue-fragment"*/
import EveImage from "../../../EveImage";
import Time from "../../../Time";
import ExtendedWalletTransactionRowComponent from "./ExtendedWalletTransactionRowComponent";

export default {
    name: "WalletTransactionRowComponent",
    components: {ExtendedWalletTransactionRowComponent, Time, EveImage, DataCell, TableRow, /*Fragment*/},
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
