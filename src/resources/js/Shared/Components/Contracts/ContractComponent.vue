<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="flex-grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Contract
        </div>
      </div>
    </template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="relative max-h-96 overflow-y-auto">
      <div class="hidden z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500 sm:grid sm:grid-cols-5 sm:gap-1 grid-flow-row ">
        <div class="px-6 py-1">
          Issuer
        </div>
        <div class="px-6 py-1">
          Assignee
        </div>
        <div class="px-6 py-1">
          Type
        </div>
        <div class="px-6 py-1">
          Title
        </div>
        <div class="px-6 py-1">
          Details
        </div>
      </div>

      <ul class="relative z-0 divide-y divide-gray-200">
        <ContractRowComponent
          v-for="contract in result"
          :key="contract.contract_id"
          :contract="contract"
          :entity="entity"
        />
        <div ref="scrollComponent"></div>
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import ContractRowComponent from "./ContractRowComponent";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
export default {
  name: "ContractComponent",
  components: {ContractRowComponent, CardWithHeader, EntityByIdBlock
  },
  props: {
    id: {
      required: true,
      type: Number
    },
    type: {
      required: false,
      type: String,
      default: 'character'
    }
  },
  setup(props) {

    return useInfinityScrolling('character.contracts.details', props.id)

  },
  computed: {
    entity() {
      return {
        type: this.type,
        character_id: this.type === 'character' ? this.id : null,
        corporation_id: this.type === 'corporation' ? this.id : null
      }
    }
  }
}
</script>

<style scoped>

</style>
