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
    <WhenVisible
      data="corporationHistory"
      :buffer="200"
    >
      <template #fallback>
        <div class="max-h-96 overflow-auto">
          <div class="p-4 sm:p-6 flow-root">
            <ul class="-mb-8">
              <li
                v-for="n in 3"
                :key="n"
              >
                <div class="relative pb-8">
                  <span
                    v-if="n !== 3"
                    class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  />
                  <div class="relative flex space-x-3 animate-pulse">
                    <div class="h-8 w-8 rounded-full bg-gray-200" />
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div class="h-4 w-1/3 rounded bg-gray-200" />
                      <div class="h-4 w-16 rounded bg-gray-200" />
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </template>
      <div class="max-h-96 overflow-auto">
        <div class="p-4 sm:p-6 flow-root">
          <ul class="-mb-8">
            <li
              v-for="(event, eventIdx) in results"
              :key="event.record_id"
            >
              <div class="relative pb-8">
                <span
                  v-if="(eventIdx !== results.length - 1)"
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
      </div>
    </WhenVisible>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ResolveIdToName from "../../ResolveIdToName.vue";
import { WhenVisible } from "@inertiajs/vue3";

export default {
    name: "CorporationHistoryComponent",
    components: {WhenVisible, ResolveIdToName, Time, EveImage, EntityBlock, CardWithHeader},
    props: {
        character: {
            type: Object,
            required: true
        }
    },
    computed: {
        // The recruit's histories arrive as the lazily-resolved `corporationHistory` shared prop —
        // a map keyed by character_id (the review page can show several recruit characters, each with
        // its own card). It is undefined until <WhenVisible> fires its first partial reload once this
        // card scrolls into view; read this card's own character's (bounded) timeline out of the map.
        results() {
            return this.$page.props.corporationHistory?.[this.character.character_id] ?? []
        }
    }
}
</script>

<style scoped>

</style>
