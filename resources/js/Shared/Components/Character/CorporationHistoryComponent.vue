<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="grow"
          :entity="character"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Corporation History
        </div>
      </div>
    </template>
    <div
      class="relative max-h-96 overflow-y-auto"
      scroll-region=""
    >
      <InfiniteScroll
        :data="scrollKey"
        :items-element="`#${scrollBodyId}`"
        preserve-url
      >
        <div class="p-4 sm:p-6 flow-root">
          <ul
            :id="scrollBodyId"
            class="-mb-8"
          >
            <li
              v-for="(event, eventIdx) in events"
              :key="event.record_id"
            >
              <div class="relative pb-8">
                <span
                  v-if="(eventIdx !== events.length - 1)"
                  class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                  aria-hidden="true"
                />
                <div class="relative flex space-x-3">
                  <div>
                    <EveImage
                      :object="{corporation_id: event.corporation_id}"
                      tailwind_class="h-8 w-8 rounded-full"
                      :size="256"
                    />
                  </div>
                  <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                    <div>
                      <p class="text-sm text-gray-500">
                        <ResolveIdToName
                          :id="event.corporation_id"
                          tailwind-class="font-medium text-gray-900"
                        />
                      </p>
                    </div>
                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                      <Time :timestamp="event.start_date" />
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </InfiniteScroll>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { InfiniteScroll } from "@inertiajs/vue3";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ResolveIdToName from "../../ResolveIdToName.vue";

export default {
    name: "CorporationHistoryComponent",
    components: {ResolveIdToName, Time, EveImage, InfiniteScroll, EntityBlock, CardWithHeader},
    props: {
        character: {
            type: Object,
            required: true
        }
    },
    computed: {
        // The hosting page (CharacterInspectionScrollProps) supplies one scroll prop per inspected
        // character, so read this character's history straight off the page props.
        scrollKey() {
            return `corporation_history_${this.character.character_id}`
        },
        scrollBodyId() {
            return `corporation-history-body-${this.character.character_id}`
        },
        events() {
            return this.$page.props[this.scrollKey]?.data ?? []
        }
    }
}
</script>

<style scoped>

</style>
