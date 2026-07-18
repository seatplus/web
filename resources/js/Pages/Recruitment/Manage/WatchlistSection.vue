<template>
  <div class="border-t border-gray-100 pt-3 space-y-3">
    <button
      type="button"
      class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
      @click="open = !open"
    >
      {{ open ? 'Hide' : 'Manage' }} watchlist
    </button>

    <div
      v-if="open"
      class="space-y-4"
    >
      <p class="text-xs text-gray-500">
        Items, assets and contracts matching this watchlist are highlighted while reviewing an
        applicant (and, later, while observing an employee). Saved together with the review stages.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <EsiMultiselect
          v-model="form.regions"
          :categories="['region']"
          label="Regions"
          placeholder="Search for a region"
        />
        <EsiMultiselect
          v-model="form.systems"
          :categories="['solar_system']"
          label="Solar systems"
          placeholder="Search for a solar system"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Item types, groups or categories</label>
        <Autosuggest
          :key="itemsKey"
          route-name="autosuggestion.typesOrGroupOrCategories"
          placeholder="Search for items"
          @selectedObject="addItem"
        />
        <div class="mt-2 flex flex-wrap gap-2">
          <DismissibleButton
            v-for="item in form.items"
            :id="item.id"
            :key="item.id"
            :name="item.name"
            @remove="removeItem"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import Autosuggest from "@/Shared/Components/Autosuggest.vue";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";

// The parent posting card's Inertia form; the watchlist fields are part of it so everything saves
// together with a single button.
const props = defineProps({
  form: {
    required: true,
    type: Object,
  },
});

const open = ref(false);
// Re-key the items autosuggest after each selection so it clears its input.
const itemsKey = ref(0);

function addItem(selection) {
  if (!props.form.items.some((item) => item.id === selection.id)) {
    props.form.items.push(selection);
  }
  itemsKey.value++;
}

function removeItem(id) {
  props.form.items = props.form.items.filter((item) => item.id !== id);
}
</script>
