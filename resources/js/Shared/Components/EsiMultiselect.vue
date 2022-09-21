<template>
  <div>
    <EsiAutosuggest
      :key="uniqueID"
      :label="label"
      :placeholder="placeholder"
      :categories="categories"
      @selected-object="(obj) => selections.push(obj)"
    />
    <DismissibleButton
      v-for="selection in selections"
      :id="selection.id"
      :key="selection.id"
      :name="selection.name"
      @remove="(id) => selections = selections.filter((obj) => obj.id !== id)"
    />
  </div>
</template>

<script setup>

import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";
import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
import {ref, watchEffect} from "vue";


const props = defineProps({
    modelValue: {
        required: true,
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
    }
});

const selections = ref(props.modelValue);
const uniqueID = ref(+new Date());

const emits = defineEmits(['update:modelValue']);

watchEffect(() => {
    emits('update:modelValue', selections.value);
    uniqueID.value++
});

</script>
