<template>
  <li
    class="px-6 py-5 relative cursor-pointer"
    @click="toggle"
  >
    <div class="group flex justify-between items-center space-x-2">
      <div class="-m-1 p-1 block">
        <span class="absolute inset-0 group-hover:bg-gray-50" />
        <EntityBlock
          :entity="entity"
          class="flex-1 flex items-center min-w-0 relative"
        />
      </div>
      <div class="relative inline-block text-left">
        <svg
          v-if="isSelected"
          class="h-8 w-8 text-green-400"
          fill="none"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
    </div>
  </li>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
export default {
    name: "SelectionEntity",
    components: {EntityBlock},
    props: {
        entity: {
            type: Object,
            required: true
        },
        modelValue: {
            type: Array,
            default: () => []
        }
    },
    emits: ['update:modelValue'],
    computed: {
        isSelected() {
            return this.modelValue.includes(this.entity.id)
        }
    },
    methods: {
        toggle() {

            const entity = this.entity
            let selected = this.modelValue
            let index = selected.indexOf(entity.id)


            if(index >= 0) {
                selected = _.remove(selected, (select) => select !== entity.id)
            } else {
                selected.push(entity.id)
            }

            this.$emit('update:modelValue', selected)
        }
    }
}
</script>

<style scoped>

</style>
