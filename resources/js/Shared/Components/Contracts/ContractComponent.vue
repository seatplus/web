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
      <InfiniteLoadingHelper
        v-slot="{results}"
        route="character.contracts.details"
        :params="parameters"
      >
        <StickyHeaderTable
          :header-titles="[{title: 'Issuer', columnSpan: 2}, {title: 'Assignee', columnSpan: 2}, {title: 'Type', columnSpan: 2}, {title: 'Details', columnSpan: 4}, {title: 'Content', columnSpan: 1, srOnly: true}]"
        >
          <template #default="slotProps">
            <StickyHeaderTableRow
              v-for="contract in results"
              :key="contract.contract_id"
              :number-columns="slotProps.countColumns"
            >
              <!--  Issuer-->
              <StickyHeaderCell
                :cell="slotProps.columns[0]"
                class="self-center truncate"
              >
                <IssuerComponent :contract="contract" />
              </StickyHeaderCell>
              <!--  Assignee-->
              <StickyHeaderCell
                :cell="slotProps.columns[1]"
                class="self-center truncate"
              >
                <AssigneeComponent :contract="contract" />
              </StickyHeaderCell>
              <!-- Contract type-->
              <StickyHeaderCell
                :cell="slotProps.columns[2]"
                class="flex flex-wrap text-sm font-medium text-gray-500 sm:self-center truncate"
              >
                <ContractTypeComponent :contract="contract" />
              </StickyHeaderCell>
              <StickyHeaderCell
                :cell="slotProps.columns[3]"
                class="flex flex-wrap gap-x-2 text-sm font-medium text-gray-500 sm:self-center"
              >
                <DetailsComponent :contract="contract" />
              </StickyHeaderCell>
              <StickyHeaderCell
                :cell="slotProps.columns[4]"
                class="sm:self-center sm:place-self-end"
              >
                <ExpandContractComponent
                  v-if="contract.items > 0"
                  :character-id="id"
                  :contract="contract"
                />
              </StickyHeaderCell>
            </StickyHeaderTableRow>
            <div ref="scrollComponent" />
          </template>
        </StickyHeaderTable>
      </InfiniteLoadingHelper>
    </div>
  </CardWithHeader>
</template>

<script>

import CardWithHeader from "../../Layout/Cards/CardWithHeader";
import EntityByIdBlock from "../../Layout/Eve/EntityByIdBlock";
import StickyHeaderTable from "../../Layout/Table/StickyHeaderTable";
import StickyHeaderTableRow from "../../Layout/Table/StickyHeaderTableRow";
import StickyHeaderCell from "../../Layout/Table/StickyHeaderCell";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import {ref} from "vue";
import ExpandContractComponent from "./ExpandContractComponent";
import IssuerComponent from "./Cells/IssuerComponent";
import AssigneeComponent from "./Cells/AssigneeComponent";
import ContractTypeComponent from "./Cells/ContractTypeComponent";
import DetailsComponent from "./Cells/DetailsComponent";
 
export default {
    name: "ContractComponent",
    components: {
        DetailsComponent,
        ContractTypeComponent,
        AssigneeComponent,
        IssuerComponent,
        ExpandContractComponent,
        InfiniteLoadingHelper,
        StickyHeaderCell,
        StickyHeaderTableRow,
        StickyHeaderTable, CardWithHeader, EntityByIdBlock,
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
        },
        watchlist: {
            required: false,
            type: Object,
            default: () => new Object()
        }
    },
    setup() {

        const contracts = ref([])

        const getStartLocation = (contract) => _.get(contract, 'start_location.name', _.get(contract, 'start_location.locatable.name', 'unknown'))
        const getEndLocation = (contract) => _.get(contract, 'end_location.name', _.get(contract, 'end_location.locatable.name', 'unknown'))

        return {
            contracts,
            getStartLocation,
            getEndLocation
        }

    },
    computed: {
        entity() {
            return {
                type: this.type,
                character_id: this.type === 'character' ? this.id : null,
                corporation_id: this.type === 'corporation' ? this.id : null
            }
        },
        parameters() {
            return {...this.watchlist, character_id: this.id}
        }
    }
}
</script>
