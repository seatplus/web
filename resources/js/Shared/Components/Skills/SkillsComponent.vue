<template>
  <div class="space-y-4">
    <div class="border-b border-gray-200 pb-4">
      <EntityByIdBlock :id="characterId" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <SkillQueue :character-id="characterId" />
      </div>
      <div class="col-span-2 space-y-4">
        <Skills :character-id="characterId" />
      </div>
    </div>
  </div>
</template>

<script>
import Skills from "./Skills.vue";
import SkillQueue from "./SkillQueue.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
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
