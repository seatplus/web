<template>
  <!-- Loading: pulsing skeleton while the character's skill queue is fetched. -->
  <CardWithHeader v-if="!hasLoaded">
    <template #header>
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Skill Queue
      </h3>
    </template>
    <div class="px-4 py-5 sm:px-6 space-y-4">
      <div
        v-for="n in 3"
        :key="n"
        class="flex items-center gap-3"
      >
        <div class="h-8 w-8 rounded-full bg-gray-200 animate-pulse" />
        <div class="flex-1 space-y-2">
          <div class="h-3 w-2/3 rounded bg-gray-200 animate-pulse" />
          <div class="h-3 w-1/3 rounded bg-gray-100 animate-pulse" />
        </div>
      </div>
    </div>
  </CardWithHeader>

  <!-- Loaded content: the queue, or an empty state when nothing is training. -->
  <CardWithHeader v-else>
    <template #header>
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Skill Queue
      </h3>
    </template>

    <div
      v-if="isEmpty"
      class="text-center px-4 py-12 sm:px-6"
    >
      <BookOpenIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        No skills in training.
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        This character's skill queue is empty.
      </p>
    </div>

    <div
      v-else
      class="flow-root px-4 py-5 sm:px-6"
    >
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
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { getJson } from "@/Functions/http";
import { skillQueue as skillQueueAction } from "@/actions/Seatplus/Web/Http/Controllers/Character/SkillsController";
import { BookOpenIcon } from "@heroicons/vue/20/solid";
import Time from "@/Shared/Time.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";

const props = defineProps({
    characterId: {
        type: Number,
        required: true,
    },
});

const results = ref([]);
const hasLoaded = ref(false);

const queue = computed(() => _.chain(results.value)
    .map((item) => ({
        ...item,
        name: _.get(item, "type.name"),
    }))
    .sortBy(["queue_position"])
    .value());

const isEmpty = computed(() => queue.value.length === 0);

onMounted(() => {
    getJson(skillQueueAction.url(props.characterId))
        .then((response) => {
            // The controller returns an Eloquent collection (bare array); tolerate a
            // resource-collection ({ data: [...] }) shape too.
            results.value = response?.data ?? response ?? [];
        })
        .finally(() => {
            hasLoaded.value = true;
        });
});
</script>

<style scoped>

</style>
