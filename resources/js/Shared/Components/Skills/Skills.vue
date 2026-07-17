<template>
  <!-- Loading: pulsing skeleton while the character's skills are fetched. -->
  <div
    v-if="!hasLoaded"
    class="space-y-4"
  >
    <div
      v-for="n in 2"
      :key="n"
      class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200"
    >
      <div class="px-4 py-5 sm:px-6">
        <div class="h-4 w-1/3 rounded bg-gray-200 animate-pulse" />
      </div>
      <div class="px-4 py-5 sm:px-6 space-y-3">
        <div
          v-for="row in 3"
          :key="row"
          class="h-3 w-2/3 rounded bg-gray-100 animate-pulse"
        />
      </div>
    </div>
  </div>

  <!-- Empty: the character has no trained skills yet. -->
  <div
    v-else-if="isEmpty"
    class="text-center py-12"
  >
    <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
    <h3 class="mt-2 text-sm font-semibold text-gray-900">
      No skills found.
    </h3>
    <p class="mt-1 text-sm text-gray-500">
      This character has no trained skills yet, or the data has not been fetched.
    </p>
  </div>

  <!-- One card per skill group, matching the modern SkillQueue card. -->
  <template v-else>
    <CardWithHeader
      v-for="(groupSkills, group) in groupedSkills"
      :key="group"
    >
      <template #header>
        <div class="flex flex-wrap items-center justify-between gap-2">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ group }}
          </h3>
          <div class="flex items-center space-x-3 text-sm text-gray-500">
            <span>{{ sumSkillpoints(groupSkills) }} total skillpoints</span>
            <div class="flex space-x-1.5">
              <div class="flex">
                <div class="shrink-0 self-center">
                  <StarIcon class="h-4 w-4" />
                </div>
                <span>active</span>
              </div>
              <div class="flex">
                <div class="shrink-0 self-center">
                  <StarIconOutline class="h-4 w-4" />
                </div>
                <span>trained</span>
              </div>
            </div>
          </div>
        </div>
      </template>
      <dl class="divide-y divide-gray-200 px-4 sm:px-6">
        <LeftAlignedData
          v-for="skill in groupSkills"
          :key="skill.skill_id"
        >
          <template #title>
            {{ skill.name }}
          </template>
          <template #description>
            <div class="flex justify-end">
              <div
                v-for="level in levels(skill)"
                :key="level.key"
              >
                <StarIcon
                  v-if="level.active"
                  class="h-4 w-4"
                />
                <StarIconOutline
                  v-else
                  class="h-4 w-4"
                />
              </div>
            </div>
          </template>
        </LeftAlignedData>
      </dl>
    </CardWithHeader>
  </template>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { getJson } from "@/Functions/http";
import { skills as skillsAction } from "@/actions/Seatplus/Web/Http/Controllers/Character/SkillsController";
import LeftAlignedData from "../../Layout/DataDisplay/LeftAlignedData.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import { StarIcon } from "@heroicons/vue/20/solid";
import { StarIcon as StarIconOutline, AcademicCapIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    characterId: {
        type: Number,
        required: true,
    },
});

const results = ref([]);
const hasLoaded = ref(false);

const groupedSkills = computed(() => _.chain(results.value)
    .map((skill) => ({
        ...skill,
        name: _.get(skill, "type.name"),
        group: _.get(skill, "type.group.name"),
    }))
    .groupBy("group")
    .value());

const isEmpty = computed(() => results.value.length === 0);

const sumSkillpoints = (skillGroup) => _.sumBy(skillGroup, "skillpoints_in_skill").toLocaleString();

const levels = (skill) => {
    const trainedLevels = [];

    for (let i = 0; i < skill.trained_skill_level; i++) {
        trainedLevels.push({
            key: `${skill.skill_id}:${i}`,
            active: i <= skill.active_skill_level,
        });
    }

    return trainedLevels;
};

onMounted(() => {
    getJson(skillsAction.url(props.characterId))
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
