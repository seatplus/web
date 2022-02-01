<template>
  <div class="relative">
    <div
      class="absolute inset-0 flex items-center"
      aria-hidden="true"
    >
      <div class="w-full border-t border-gray-300" />
    </div>
    <div class="relative flex justify-start">
      <span class="pr-3 bg-gray-100 text-lg font-medium text-gray-900">
        <EntityByIdBlock
          :id="characterId"
          class="flex-grow"
        />
      </span>
    </div>
  </div>
  <div class="grid flex grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
      <SkillQueue :character-id="characterId" />
    </div>
    <div class="col-span-2 space-y-4">
      <Skills :character-id="characterId" />
    </div>
  </div>
</template>

<script>
import Skills from "./Skills";
import SkillQueue from "./SkillQueue";
import EntityByIdBlock from "../../Layout/Eve/EntityByIdBlock";
export default {
    name: "SkillsComponent",
    components: {EntityByIdBlock, SkillQueue, Skills},
    props: {
        characterId: {
            type: Number,
            required: true
        }
    }
}
</script>

<style scoped>

</style>