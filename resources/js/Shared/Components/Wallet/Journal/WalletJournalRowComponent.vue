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
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Type
      </label>
      {{ getTranslation(entry) }}
    </div>
    <div class="px-6 py-4 sm:py-1 self-center truncate sm:col-span-3 text-right">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Amount
      </label>
      {{ entry.amount ? entry.amount.toLocaleString() : '' }}
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center truncate sm:col-span-3 text-right">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Balance
      </label>
      {{ entry.balance ? entry.balance.toLocaleString() : '' }}
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center truncate text-right sm:col-span-2 col-span-2">
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
    <ExtendedWalletJournalRowComponent
      v-if="expanded"
      class="col-span-2 sm:col-span-12 px-6 sm:px-3 py-4 sm:py-1"
      :entry="entry"
    />
  </li>
<!--  <TableRow :class="even ? 'bg-gray-50' : 'bg-white'">
    <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
      <Time :timestamp="entry.date" />
    </DataCell>
    <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
      {{ getTranslation(entry) }}
    </DataCell>
    &lt;!&ndash;<DataCell class="px-6 py-4 truncate whitespace-nowrap text-sm text-gray-500">
                {{ entry.description }}
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ entry.first_party_id }}
            </DataCell>
            <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ entry.second_party_id }}
            </DataCell>&ndash;&gt;
    <DataCell class="px-6 py-4 whitespace-normal text-right text-sm text-gray-500">
      {{ entry.amount ? entry.amount.toLocaleString() : '' }}
    </DataCell>
    <DataCell class="px-6 py-4 whitespace-normal text-right text-sm text-gray-500">
      {{ entry.balance ? entry.balance.toLocaleString() : '' }}
    </DataCell>
    <DataCell class="px-6 py-4 whitespace-normal text-right text-sm text-gray-500">
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
    </DataCell>
  </TableRow>
  <TableRow
    v-if="expanded"
    :class="even ? 'bg-gray-50' : 'bg-white'"
  >
    <DataCell
      colspan="5"
      class="px-6 pb-4 whitespace-nowrap text-sm text-gray-500"
    >
      <ExtendedWalletJournalRowComponent :entry="entry" />
    </DataCell>
  </TableRow>-->
</template>

<script>
import Time from "@/Shared/Time";
import ExtendedWalletJournalRowComponent from "./ExtendedWalletJournalRowComponent";

export default {
    name: "WalletJournalRowComponent",
    components: {
        ExtendedWalletJournalRowComponent, Time
    },
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
    methods: {
        getTranslation(entry) {

            let string = 'web::wallet_journal.' + _.toString(entry.ref_type)

            return this.$I18n.trans(string)
        },
        toggle() {
            this.expanded = !this.expanded
        }
    }
}
</script>

<style scoped>

</style>
