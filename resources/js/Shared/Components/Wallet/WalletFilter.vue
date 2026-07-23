<template>
  <Card>
    <div class="space-y-2">
      <label
        for="wallet-ref-type-filter"
        class="block text-sm font-medium text-gray-700"
      >
        Wallet Journal entries
      </label>
      <input
        id="wallet-ref-type-filter"
        v-model="query"
        type="text"
        name="wallet-ref-type-filter"
        placeholder="Filter journal by ref type…"
        class="shadow-xs focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
      >

      <ul
        v-if="query.length && filteredOptions.length"
        class="max-h-40 overflow-auto rounded-md border border-gray-200 divide-y divide-gray-100 text-sm"
      >
        <li
          v-for="option in filteredOptions"
          :key="option"
          class="px-3 py-2 cursor-pointer hover:bg-indigo-600 hover:text-white"
          @click="select(option)"
        >
          {{ option }}
        </li>
      </ul>

      <div
        v-if="selected.length"
        class="flex flex-wrap gap-1"
      >
        <DismissibleButton
          v-for="name in selected"
          :id="name"
          :key="name"
          :name="name"
          @remove="remove"
        />
      </div>
    </div>
  </Card>
</template>

<script setup>
import { computed, ref } from "vue";
import Card from "../../Layout/Cards/Card.vue";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";

const props = defineProps({
    // Selected ref_type strings (v-model).
    modelValue: {
        required: true,
        type: Array
    },
    // Available ref_type options, provided by the controller (no autosuggest
    // endpoint — hence no axios / Ziggy on this page).
    refTypes: {
        required: false,
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const query = ref('')

const selected = computed(() => props.modelValue)

const filteredOptions = computed(() => {
    const q = query.value.toLowerCase()

    return props.refTypes
        .filter((type) => !selected.value.includes(type))
        .filter((type) => type.toLowerCase().includes(q))
        .slice(0, 20)
})

function select(option) {
    emit('update:modelValue', [...selected.value, option])
    query.value = ''
}

function remove(name) {
    emit('update:modelValue', selected.value.filter((type) => type !== name))
}
</script>

<style scoped>

</style>
