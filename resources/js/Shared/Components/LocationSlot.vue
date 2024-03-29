<template>
  <CardWithHeader v-if="isShown">
    <template #header>
      <h3 class="text-lg leading-6 font-medium text-gray-900 cap">
        {{ name }}
      </h3>
    </template>
    <wide-lists>
      <template #elements>
        <wide-list-element
          v-for="(asset, index) in filtered_items"
          :key="asset.item_id"
          :url="url(asset)"
        >
          <template #avatar>
            <span class="inline-block relative">
              <EveImage
                :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
                :object="asset.type"
                :size="128"
              />
              <span
                v-if="asset.quantity > 1"
                class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400"
              >
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ asset.quantity }}</span>
              </span>
            </span>
          </template>

          <template #upper_left>
            {{ asset.name }}
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

            <span class="truncate">{{ asset.type.name }}</span>
          </template>

          <template #upper_right>
            {{ asset.type.group.name }} <span
              v-if="!asset.is_singleton"
              class="text-info"
            >(packaged)</span>
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
            {{ getMetricPrefix(asset.quantity * asset.type.volume) }}
          </template>

          <template #navigation>
            <svg
              v-if="hasContent(asset.content)"
              :class="[{'text-gray-400' : hasContent(asset.content), 'text-transparent' : !hasContent(asset.content)},'h-5 w-5']"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </template>
        </wide-list-element>
      </template>
    </wide-lists>
  </CardWithHeader>
</template>

<script>
import WideLists from "../WideLists.vue";
import WideListElement from "../WideListElement.vue";
import EveImage from "../EveImage.vue"
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import { prefix } from "metric-prefix";

export default {
    name: "LocationSlot",
    components: {CardWithHeader, WideListElement, WideLists, EveImage},
    props: {
        name: {
            type: String,
            required: true
        },
        items: {
            type: Array,
            required: true
        },
        slots: {
            type: Array,
            required: true
        }
    },
    computed: {
        filtered_items() {
            return _.sortBy(_.each(_.filter(this.items, (item) => this.slots.includes(item.location_flag)), (item) => item.index = this.slots.indexOf(item.location_flag)), 'index')
        },
        isShown() {
            return !_.isEmpty(this.filtered_items)
        }
    },
    methods: {
        getMetricPrefix(numeric_value) {

            return prefix(numeric_value, {precision: 3, unit: 'm³'})
        },
        url(asset) {

            return _.isEmpty(asset.content) ? '' : route('character.item', {'item_id': asset.item_id, 'character_id': asset.owner.character_id})
        },
        hasContent(content) {
            return !_.isEmpty(content)
        }
    }
}
</script>

<style scoped>

</style>
