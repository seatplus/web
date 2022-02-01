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
import { CheckIcon, ThumbUpIcon, UserIcon, BookOpenIcon } from '@heroicons/vue/solid'
import Time from "../../Time";
import CardWithHeader from "../../Layout/Cards/CardWithHeader";

const timeline = [
    {
        id: 1,
        content: 'Applied to',
        target: 'Front End Developer',
        href: '#',
        date: 'Sep 20',
        datetime: '2020-09-20',
        icon: UserIcon,
        iconBackground: 'bg-gray-400',
    },
    {
        id: 2,
        content: 'Advanced to phone screening by',
        target: 'Bethany Blake',
        href: '#',
        date: 'Sep 22',
        datetime: '2020-09-22',
        icon: ThumbUpIcon,
        iconBackground: 'bg-blue-500',
    },
    {
        id: 3,
        content: 'Completed phone screening with',
        target: 'Martha Gardner',
        href: '#',
        date: 'Sep 28',
        datetime: '2020-09-28',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
    {
        id: 4,
        content: 'Advanced to interview by',
        target: 'Bethany Blake',
        href: '#',
        date: 'Sep 30',
        datetime: '2020-09-30',
        icon: ThumbUpIcon,
        iconBackground: 'bg-blue-500',
    },
    {
        id: 5,
        content: 'Completed interview with',
        target: 'Katherine Snyder',
        href: '#',
        date: 'Oct 4',
        datetime: '2020-10-04',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
]

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
            queue,
            timeline
        }
    }
}
</script>

<style scoped>

</style>