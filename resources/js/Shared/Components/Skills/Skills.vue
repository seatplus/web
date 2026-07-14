<template>
  <div
    v-for="(groupSkills, group) in skills"
    :key="group"
  >
    <LeftAligned>
      <template #title>
        {{ group }}
      </template>
      <template #description>
        <div class="flex justify-between">
          <span>{{ sumSkillpoints(groupSkills) }} total skillpoints</span>
          <div class="flex space-x-1.5">
            <div class="flex">
              <div class="shrink-0 self-center">
                <StarIcon class="h-4 w-4" />
              </div>
              <span>
                active
              </span>
            </div>
            <div class="flex">
              <div class="shrink-0 self-center">
                <StarIconOutline class="h-4 w-4" />
              </div>
              <span>
                trained
              </span>
            </div>
          </div>
        </div>
      </template>
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
    </LeftAligned>
  </div>
</template>

<script>
import {computed, onMounted, ref} from "vue";
import { getJson } from "@/Functions/http";
import { skills as skillsAction } from "@/actions/Seatplus/Web/Http/Controllers/Character/SkillsController";
import LeftAligned from "../../Layout/DataDisplay/LeftAligned.vue";
import LeftAlignedData from "../../Layout/DataDisplay/LeftAlignedData.vue";
import {StarIcon} from "@heroicons/vue/20/solid";
import {StarIcon as StarIconOutline} from "@heroicons/vue/24/outline";

export default {
    name: "Skills",
    components: {LeftAlignedData, LeftAligned, StarIcon, StarIconOutline},
    props: {
        characterId: {
            type: Number,
            required: true
        }
    },
    setup(props) {

        // A character's skills are bounded — fetch them all in one request (axios/Ziggy-free).
        const rawSkills = ref([])
        onMounted(async () => {
            rawSkills.value = await getJson(skillsAction.url(props.characterId)) ?? []
        })

        const skills = computed(() => _.chain(rawSkills.value)
            .map((skill) => {
                return {
                    ...skill,
                    name: _.get(skill, 'type.name'),
                    group: _.get(skill, 'type.group.name')
                }
            })
            .groupBy('group')
            .value()
        )

        const sumSkillpoints = (skillgroup) => _.sumBy(skillgroup, 'skillpoints_in_skill').toLocaleString()

        const levels = (skill) => {

            let levels = []

            for (let i = 0; i < skill.trained_skill_level; i++) {
                levels.push({
                    key: `${skill.skill_id}:${skill.trained_skill_level}`,
                    active: i <= skill.active_skill_level
                })
            }

            return levels

        }

        return {
            skills,
            sumSkillpoints,
            levels
        }
    }
}
</script>

<style scoped>

</style>