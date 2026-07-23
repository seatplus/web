<template>
  <div class="space-y-4">
    <div class="border-b border-gray-200 pb-4">
      <EntityByIdBlock :id="characterId" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <SkillQueue :skill-queue="skillQueue" />
      </div>
      <div class="col-span-2 space-y-4">
        <Skills :skills="skills" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Skills from "./Skills.vue";
import SkillQueue from "./SkillQueue.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";

defineProps({
    characterId: {
        type: Number,
        required: true
    },
    skills: {
        type: Array,
        default: () => []
    },
    skillQueue: {
        type: Array,
        default: () => []
    }
});
</script>

<style scoped>

</style>
