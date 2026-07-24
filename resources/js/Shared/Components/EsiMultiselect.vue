<template>
  <div>
    <EsiAutosuggest
      :label="label"
      :placeholder="placeholder"
      :categories="categories"
      :show-label="showLabel"
      reset-after-select
      @selected-object="(obj) => { if (obj) { selections.push(obj) } }"
    />
    <div
      v-if="selections.length"
      class="mt-2 flex flex-wrap gap-2"
    >
      <DismissibleEntityButton
        v-for="selection in selections"
        :key="selection.id"
        :entity="selection"
        @remove="(id) => selections = selections.filter((obj) => obj.id !== id)"
      />
    </div>
  </div>
</template>

<script setup>

import DismissibleEntityButton from "@/Shared/Layout/Buttons/DismissibleEntityButton.vue";
import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
import {ref, watch} from "vue";


const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    categories: {
        required: true,
        type: Array
    },
    label: {
        required: true,
        type: String
    },
    placeholder: {
        required: true,
        type: String
    },
    showLabel: {
        required: false,
        type: Boolean,
        default: true
    }
});

const selections = ref(props.modelValue);

const emits = defineEmits(['update:modelValue']);

watch(selections, (value) => emits('update:modelValue', value), {deep: true});

</script>
