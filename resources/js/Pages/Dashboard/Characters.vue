<template>
  <div class="space-y-2">
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      Characters
    </h3>
    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      Below you find all characters you have added to this seatplus instance
    </p>
    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <li
        v-for="character in characters"
        :key="character.character_id"
      >
        <LeftAligned>
          <template #header>
            <EntityBlock :entity="character" />
          </template>
          <LeftAlignedData v-if="character.balance">
            <template #title>
              Balance
            </template>
            <template #description>
              ISK {{ character.balance.balance.toLocaleString() }}
            </template>
          </LeftAlignedData>
          <LeftAlignedData>
            <template #title>
              Created
            </template>
            <template #description>
              <Time :timestamp="character.birthday" />
            </template>
          </LeftAlignedData>
          <LeftAlignedData v-if="character.batch_update">
            <template #title>
              {{ character.batch_update.finished_at ? 'Last update' : 'Update started at' }}
            </template>
            <template #description>
              <Time
                v-if="character.batch_update.finished_at"
                :timestamp="character.batch_update.finished_at"
              />
              <Time
                v-else
                :timestamp="character.batch_update.started_at"
              />
            </template>
          </LeftAlignedData>
        </LeftAligned>
      </li>
      <li class="col-span-1 bg-white rounded-lg shadow flex flex-wrap content-center">
        <a
          :href="route('auth.eve')"
          type="button"
          class="py-4 inline-flex items-center justify-center items-center w-full h-full border border-transparent shadow-sm text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <!-- Heroicon name: solid/user-add -->
          <svg
            class="-ml-1 mr-3 h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
          </svg>
          Add characters
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
import LeftAligned from "@/Shared/Layout/DataDisplay/LeftAligned.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import LeftAlignedData from "@/Shared/Layout/DataDisplay/LeftAlignedData.vue";
import Time from "@/Shared/Time.vue";
export default {
    name: "Characters",
    components: {Time, LeftAlignedData, EntityBlock, LeftAligned},
    props: {
        characters: {
            type: Array,
            required: true
        },
        enlistments: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        hasOpenEnlistments() {
            return !_.isEmpty(this.enlistments)
        }
    }

}
</script>

<style scoped>

</style>
