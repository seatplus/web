<template>
  <div
    v-for="(skills, group) in skills"
    :key="group"
  >
    <LeftAligned>
      <template #title>
        {{ group }}
      </template>
      <template #description>
        <div class="flex justify-between">
          <span>{{ sumSkillpoints(skills) }} total skillpoints</span>
          <div class="flex space-x-1.5">
            <div class="flex">
              <div class="flex-shrink-0 self-center">
                <StarIcon class="h-4 w-4" />
              </div>
              <span>
                active
              </span>
            </div>
            <div class="flex">
              <div class="flex-shrink-0 self-center">
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
        v-for="skill in skills"
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
import {useLoadCompleteResource} from "../../../Functions/useLoadCompleteResource";
import {computed} from "vue";
import LeftAligned from "../../Layout/DataDisplay/LeftAligned";
import LeftAlignedData from "../../Layout/DataDisplay/LeftAlignedData";
import {StarIcon} from "@heroicons/vue/solid";
import {StarIcon as StarIconOutline} from "@heroicons/vue/outline";

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

        const results = useLoadCompleteResource('get.character.skills', {character_id: props.characterId});

        const skills = computed(() => _.chain(results.results.value)
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