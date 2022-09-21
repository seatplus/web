<template>
  <CardWithHeader v-if="queue.length > 0">
    <template #header>
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Skill Queue
      </h3>
    </template>
    <div class="flow-root px-4 py-5 sm:px-6">
      <ul class="-mb-8">
        <li
          v-for="(item, itemIdx) in queue"
          :key="item.id"
        >
          <div class="relative pb-8">
            <span
              v-if="(itemIdx !== queue.length - 1)"
              class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
              aria-hidden="true"
            />
            <div class="relative flex space-x-3">
              <div>
                <span class="bg-gray-400 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                  <BookOpenIcon
                    class="h-5 w-5 text-white"
                    aria-hidden="true"
                  />
                </span>
              </div>
              <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                <div>
                  <p class="text-sm text-gray-500">
                    {{ item.name }}
                  </p>
                </div>
                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                  <Time
                    v-if="item.finish_date"
                    :timestamp="item.finish_date"
                  />
                  <span v-else>Unknown</span>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </CardWithHeader>

<!--  <div class=" bg-white shadow overflow-hidden sm:rounded-lg">

  </div>-->
</template>

<script>
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import {computed} from "vue";
import { BookOpenIcon } from '@heroicons/vue/20/solid'
import Time from "@/Shared/Time.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";

export default {
    name: "SkillQueue",
    components: {CardWithHeader, Time, BookOpenIcon},
    props: {
        characterId: {
            type: Number,
            required: true
        }
    },
    setup(props) {
        const results = useLoadCompleteResource('get.character.skill.queue', { character_id: props.characterId })

        const queue = computed(() => _.chain(results.results.value)
            .map((item) => {
                return {
                    ...item,
                    name: _.get(item, 'type.name')
                }
            })
            .sortBy(['queue_position'])
            .value()
        )

        return {
            results,
            queue
        }
    }
}
</script>

<style scoped>

</style>