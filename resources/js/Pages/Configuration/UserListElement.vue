<template>
  <li>
    <div class="block hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 transition duration-150 ease-in-out">
      <div class="flex items-center px-4 py-4 sm:px-6">
        <div class="min-w-0 flex-1 flex items-center">
          <div class="flex overflow-x-visible">
            <EntityBlock :entity="user.mainCharacter" />
          </div>
          <div class="min-w-0 flex-1 px-4 hidden md:grid md:grid-cols-2 md:gap-4">
            <EntityBlock
              v-for="character in characters"
              :key="character.character_id"
              :entity="character"
            />
          </div>
        </div>
        <div>
          <Link
            :href="impersonateUrl"
            class="inline-flex items-center shadow-xs px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
          >
            Impersonate
          </Link>
        </div>
      </div>
      <div
        v-if="characters.length > 0"
        class="px-4 py-4 sm:px-6 truncate text-gray-500 leading-5 text-sm"
      >
        Characters: {{ characterNames }}
      </div>
    </div>
  </li>
</template>

<script setup>
import { computed } from "vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { Link } from '@inertiajs/vue3';
import { impersonate } from "@/actions/Seatplus/Web/Http/Controllers/Configuration/SeatPlusController";

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    }
});

const impersonateUrl = computed(() => impersonate.url(props.user.id))

const characters = computed(() => _.reject(props.user.characters, (character) => _.isEqual(character.character_id, props.user.mainCharacter.character_id)))

const characterNames = computed(() => _.join(_.map(characters.value, (character) => character.name), ', '))
</script>

<style scoped>

</style>
