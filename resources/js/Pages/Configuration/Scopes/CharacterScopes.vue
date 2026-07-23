<template>
  <div>
    <h3 class="text-lg font-medium leading-6 text-gray-900">
      Character Scopes
    </h3>
    <div class="space-y-1">
      <ScopeToggle
        v-for="flavour in flavours"
        :key="`${flavour.text}:${scopesAsString}`"
        v-model:selected="selected"
        :scope="flavour"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import ScopeToggle from "./ScopeToggle.vue";

const props = defineProps({
    scopes: {
        type: Object,
        required: true
    },
    selectedScopes: {
        type: Array,
        required: false,
        default: () => []
    }
});

const emit = defineEmits(['update:selectedScopes']);

const selected = ref(props.selectedScopes)

const flavours = computed(() => _.map(props.scopes, (value, prop) => ({
    text: _.capitalize(prop),
    value: value
})))

const scopesAsString = computed(() => _.toString(props.selectedScopes))

watch(selected, (newValue) => {
    emit('update:selectedScopes', newValue)
})
</script>

<style scoped>

</style>
