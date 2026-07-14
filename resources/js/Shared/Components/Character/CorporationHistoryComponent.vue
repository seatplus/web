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
    <div class="max-h-96 overflow-auto">
      <div class="p-4 sm:p-6 flow-root">
        <!-- Loading skeleton -->
        <div
          v-if="loading"
          class="space-y-4 animate-pulse"
        >
          <div
            v-for="n in 3"
            :key="n"
            class="flex space-x-3"
          >
            <div class="h-8 w-8 rounded-full bg-gray-200" />
            <div class="flex-1 space-y-2 py-1">
              <div class="h-3 w-1/3 rounded bg-gray-200" />
            </div>
          </div>
        </div>

        <p
          v-else-if="!results.length"
          class="text-sm text-gray-500"
        >
          No corporation history.
        </p>

        <ul
          v-else
          class="-mb-8"
        >
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
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ResolveIdToName from "../../ResolveIdToName.vue";
import { getJson } from "@/Functions/http";
import { index as corporationHistory } from "@/actions/Seatplus/Web/Http/Controllers/Character/CorporationHistoryController";

export default {
    name: "CorporationHistoryComponent",
    components: {ResolveIdToName, Time, EveImage, EntityBlock, CardWithHeader},
    props: {
        character: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            results: [],
            loading: true,
        }
    },
    async mounted() {
        // A character's corporation history is bounded, so fetch it all in one request
        // (axios/Ziggy-free: native fetch via http.js + a Wayfinder action).
        try {
            this.results = await getJson(corporationHistory.url(this.character.character_id)) ?? []
        } finally {
            this.loading = false
        }
    }
}
</script>

<style scoped>

</style>
