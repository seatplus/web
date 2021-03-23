<template>
  <li
    :class="{'cursor-pointer':contract.items > 0}"
    class="bg-white grid grid-cols-2 sm:grid-cols-5 sm:gap-1 grid-flow-row hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
    @click="openDetails"
  >
    <div class="px-6 py-4 sm:py-1 self-center truncate">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Issuer
      </label>
      <EntityByIdBlock
        :id="contract.for_corporation ? contract.issuer_corporation_id : contract.issuer_id"
        :image-size="10"
        name-font-size="sm"
      />

      <EntityByIdBlock
        v-if="contract.for_corporation"
        :id="contract.for_corporation ? contract.issuer_corporation_id : contract.issuer_id"
        class="ml-6"
        :image-size="6"
        name-font-size="xs"
      />
    </div>
    <div class="px-6 py-1 self-center truncate">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Assignee
      </label>
      <EntityByIdBlock
        :id="contract.assignee_id"
        :image-size="10"
        name-font-size="sm"
      />

      <EntityByIdBlock
        v-if="contract.acceptor_id !== 0 && contract.acceptor_id !== contract.assignee_id"
        :id="contract.acceptor_id"
        class="ml-6"
        :image-size="6"
        name-font-size="xs"
      />
    </div>
    <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Contract Type
      </label>
      {{ contract.type }}

      <p
        v-if="contract.type !== 'courier'"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        {{ startLocation }}
      </p>

      <p
        v-if="contract.type === 'courier'"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        Start: {{ startLocation }}
      </p>
      <p
        v-if="contract.type === 'courier'"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        End: {{ endLocation }}
      </p>
    </div>
    <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Contract Title
      </label>
      {{ contract.title }}
    </div>
    <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Status
      </label>

      <p class="flex items-center text-sm text-gray-500">
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.05 3.636a1 1 0 010 1.414 7 7 0 000 9.9 1 1 0 11-1.414 1.414 9 9 0 010-12.728 1 1 0 011.414 0zm9.9 0a1 1 0 011.414 0 9 9 0 010 12.728 1 1 0 11-1.414-1.414 7 7 0 000-9.9 1 1 0 010-1.414zM7.879 6.464a1 1 0 010 1.414 3 3 0 000 4.243 1 1 0 11-1.415 1.414 5 5 0 010-7.07 1 1 0 011.415 0zm4.242 0a1 1 0 011.415 0 5 5 0 010 7.072 1 1 0 01-1.415-1.415 3 3 0 000-4.242 1 1 0 010-1.415zM10 9a1 1 0 011 1v.01a1 1 0 11-2 0V10a1 1 0 011-1z"
            clip-rule="evenodd"
          />
        </svg>
        {{ contract.status }}
      </p>

      <p
        v-if="contract.reward > 0"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        Reward {{ contract.reward.toLocaleString() }}
      </p>
      <p
        v-if="contract.collateral > 0"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        Collateral {{ contract.collateral.toLocaleString() }}
      </p>
      <p
        v-if="contract.price > 0"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
          />
        </svg>
        Price {{ contract.price.toLocaleString() }}
      </p>

      <p
        v-if="contract.volume > 0"
        class="flex items-center text-sm text-gray-500"
      >
        <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
          <path
            fill-rule="evenodd"
            d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        Volume {{ contract.volume.toLocaleString() }}
      </p>
    </div>
  </li>
</template>

<script>
import EntityByIdBlock from "../../Layout/Eve/EntityByIdBlock";
export default {
    name: "ContractRowComponent",
    components: {EntityByIdBlock},
    props: {
        contract: {
            type: Object,
            required: true
        },
        entity: {
            type: Object,
            required: false
        }
    },
    computed: {
        startLocation() {
            return _.get(this.contract, 'start_location.name', 'unknown')
        },
        endLocation() {
            return _.get(this.contract, 'end_location.name', 'unknown')
        }
    },
    methods: {
        openDetails() {

            if(!this.entity)
                return

            let url = '#'

            if(this.entity.type === 'character')
                url = this.$route('contract.details',{
                    character_id: this.entity.character_id,
                    contract_id: this.contract.contract_id
                });

            this.$inertia.get(url)
        }
    }
}
</script>

<style scoped>

</style>
