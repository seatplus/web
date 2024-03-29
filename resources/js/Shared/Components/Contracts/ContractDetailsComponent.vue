<template>
  <div class="space-y-4 mt-4">
    <Card>
      <div class="grid grid-cols-2 sm:gap-1 grid-flow-row sm:grid-cols-4">
        <div>
          <IssuerComponent :contract="contract" />
        </div>
        <div>
          <AssigneeComponent :contract="contract" />
        </div>
        <div>
          <ContractTypeComponent :contract="contract" />
        </div>
        <div>
          <DetailsComponent :contract="contract" />
        </div>
      </div>
    </Card>
    <div class="grid gap-4">
      <CardWithHeader v-if="requested_items.length > 0">
        <template #header>
          Requested Items
        </template>
        <wide-lists class="max-h-96 overflow-y-auto">
          <template #elements>
            <wide-list-element
              v-for="item in requested_items"
              :key="item.record_id"
            >
              <template #avatar>
                <span class="inline-block relative">
                  <EveImage
                          v-if="item.type"
                    :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
                    :object="item.type"
                    :size="128"
                    :bpo="item.raw_quantity === -1"
                  />
                  <span
                    v-if="item.quantity > 1"
                    class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400"
                  >
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ item.quantity }}</span>
                  </span>
                </span>
              </template>

              <template #upper_left>
                {{ item.name }}
              </template>

              <template #lower_left>
                <svg
                  class="shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                  />
                </svg>

                <span class="truncate">{{ item.type?.name ?? 'Unknown' }}</span>
              </template>

              <template #upper_right>
                {{ item.type?.group.name ?? 'Unknown' }}
                <span
                  v-if="isBPO(item)"
                  class="text-info"
                >
                  (Blueprint Original)
                </span>
                <span
                  v-else-if="isBlueprint(item)"
                  class="text-info"
                >
                  (Blueprint Copy)
                </span>
                <span
                  v-else-if="!item.is_singleton"
                  class="text-info"
                >
                  (packaged)
                </span>
              </template>

              <template #lower_right>
                <svg
                  class="shrink-0 mr-1.5 h-5 w-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ item.type ? getMetricPrefix(item.quantity * item.type.volume) : 'not available' }}
              </template>
            </wide-list-element>
          </template>
        </wide-lists>
      </CardWithHeader>
      <CardWithHeader v-if="included_items.length > 0">
        <template #header>
          Included Items
        </template>
        <wide-lists class="max-h-96 overflow-y-auto">
          <template #elements>
            <wide-list-element
              v-for="item in included_items"
              :key="item.record_id"
            >
              <template #avatar>
                <span class="inline-block relative">
                  <EveImage
                          v-if="item.type"
                    :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
                    :object="item.type"
                    :size="128"
                    :bpo="item.raw_quantity === -1"
                  />
                  <span
                    v-if="item.quantity > 1"
                    class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400"
                  >
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ item.quantity }}</span>
                  </span>
                </span>
              </template>

              <template #upper_left>
                {{ item.name }}
              </template>

              <template #lower_left>
                <svg
                  class="shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                  />
                </svg>

                <span class="truncate">{{ item.type?.name }}</span>
              </template>

              <template #upper_right>
                {{ item.type?.group.name }}
                <span
                  v-if="isBPO(item)"
                  class="text-info"
                >
                  (Blueprint Original)
                </span>
                <span
                  v-else-if="isBlueprint(item)"
                  class="text-info"
                >
                  (Blueprint Copy)
                </span>
                <span
                  v-else-if="!item.is_singleton"
                  class="text-info"
                >
                  (packaged)
                </span>
              </template>

              <template #lower_right>
                <svg
                  class="shrink-0 mr-1.5 h-5 w-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ item.type ? getMetricPrefix(item.quantity * item.type.volume) : 'not available' }}
              </template>
            </wide-list-element>
          </template>
        </wide-lists>
      </CardWithHeader>
    </div>
  </div>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import {prefix} from "metric-prefix";
import WideListElement from "../../WideListElement.vue";
import EveImage from "@/Shared/EveImage.vue"
import WideLists from "../../WideLists.vue";
import AssigneeComponent from "./Cells/AssigneeComponent.vue";
import {useValidateObject} from "@/Functions/useValidateObject";
import IssuerComponent from "./Cells/IssuerComponent.vue";
import ContractTypeComponent from "./Cells/ContractTypeComponent.vue";
import DetailsComponent from "./Cells/DetailsComponent.vue";
import Card from "../../Layout/Cards/Card.vue";

const schema = {
    items: value => _.isArray(value),
}

schema.items.required = true

export default {
    name: "ContractDetailsComponent",
    components: {
        Card,
        DetailsComponent,
        ContractTypeComponent,
        IssuerComponent, AssigneeComponent, WideLists, EveImage, WideListElement, CardWithHeader},
    props: {
        contract: {
            required: true,
            type: Object,
            validator: value => useValidateObject(value, schema)
        }
    },
    computed: {
        requested_items() {
            return _.filter(this.contract.items, 'is_included')
        },
        included_items() {
            return _.filter(this.contract.items, !'is_included')
        }
    },
    methods: {
        getMetricPrefix(numeric_value) {

            return prefix(numeric_value, {precision: 3, unit: 'm³'})
        },
        isBlueprint(item) {
            return _.get(item, 'type.category.name') === 'Blueprint'
        },
        isBPO(item) {
            return this.isBlueprint(item) && item.raw_quantity === -1
        }
    }
}
</script>

<style scoped>

</style>