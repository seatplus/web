<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="flex-grow"
          :entity="character"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Corporation History
        </div>
      </div>
    </template>
    <div class="max-h-96 overflow-auto">
      <InfiniteLoadingHelper
        v-slot="{results}"
        route="corporation.history"
        :params="{ character_id: character.character_id }"
      >
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
      </InfiniteLoadingHelper>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "../../Layout/Cards/CardWithHeader";
import EntityBlock from "../../Layout/Eve/EntityBlock";
import route from 'ziggy'
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import {ref} from "vue";
import EveImage from "../../EveImage";
import Time from "../../Time";
import ResolveIdToName from "../../ResolveIdToName";

export default {
    name: "CorporationHistoryComponent",
    components: {ResolveIdToName, Time, EveImage, InfiniteLoadingHelper, EntityBlock, CardWithHeader},
    props: {
        character: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>

</style>