<template>
  <div class="sm:-mx-6">
    <LeftAlignedData>
      <template #title>
        {{ character.name }} last updated
      </template>
      <template #description>
        <div class="space-x-2">
          <Time
            v-if="batchUpdate.finished_at && !isUpdating"
            :timestamp="batchUpdate.finished_at"
          />
          <Button
            v-if="canUpdate && !isUpdating"
            button-size="xs"
            :is-inertia-button="false"
            @click="updateCharacter"
          >
            Update
          </Button>
          <div
            v-if="isUpdating"
            class="inline-flex items-center"
          >
            updating
            <svg
              class="animate-spin h-4 ml-1 text-gray-400"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
          </div>
        </div>
      </template>
    </LeftAlignedData>
  </div>
</template>

<script>
import LeftAlignedData from "@/Shared/Layout/DataDisplay/LeftAlignedData.vue";
import Button from "@/Shared/Layout/Button.vue";
import {computed, ref, watch} from "vue";
import dayjs from "dayjs";
import Time from "@/Shared/Time.vue";
import { getJson, post } from "@/Functions/http";
import { getBatchUpdate, dispatchBatchUpdate } from "@/actions/Seatplus/Web/Http/Controllers/Recruitment/ApplicationsController";

export default {
    name: "UpdateCharacterComponent",
    components: {Time, Button, LeftAlignedData},
    props: {
        character: {
            type: Object,
            required:true
        }
    },
    setup(props) {

        const batchUpdate = ref(props.character.batch_update != null ? props.character.batch_update : {finished_at: null})
        const isUpdating = ref(false)
        const interval = ref()

        const canUpdate = computed(() => _.isNil(batchUpdate.value.finished_at) || dayjs(batchUpdate.value.finished_at).isBefore(dayjs().subtract(1,'hour')))

        const getUpdate = async () => getJson(getBatchUpdate.url(props.character.character_id))
            .then((data) => {
                batchUpdate.value = data

                if (data?.finished_at) {
                    isUpdating.value = false
                }
            })

        const updateCharacter = function () {
            isUpdating.value = true;

            post(dispatchBatchUpdate.url(props.character.character_id))
        }

        watch(isUpdating,(newValue) => {
            newValue ? interval.value = setInterval(getUpdate,15000) : clearInterval(interval.value)
        })

        return {
            batchUpdate,
            canUpdate,
            isUpdating,
            updateCharacter
        }
    }
}
</script>

<style scoped>

</style>