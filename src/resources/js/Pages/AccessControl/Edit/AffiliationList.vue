<template>
  <div :class="['bg-white sm:rounded-md', {'shadow' : !isEmpty}]">
    <ListTransition
      :entries="list"
      :class="'divide-y divide-grey-200'"
    >
      <div
        v-for="(entity, index) of list"
        :key="index"
        class="px-4 py-4 flex items-center justify-between sm:px-6 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out"
      >
        <EntityByIdBlock :id="entity.id" />

        <button
          :class="['text-red-500 hover:bg-red-100 focus:bg-red-100','inline-flex rounded-md p-1.5 focus:outline-none transition ease-in-out duration-150']"
          @click="removeEntity(entity)"
        >
          <svg
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </ListTransition>
  </div>
</template>

<script>
import ListTransition from "@/Shared/Transitions/ListTransition"
import {computed} from "vue";
import EntityByIdBlock from "../../../Shared/Layout/Eve/EntityByIdBlock";
export default {
    name: "AffiliationList",
    components: {EntityByIdBlock, ListTransition},
    props: {
        modelValue: {
            type: Array,
            default: () => []
        },
        type: {
            type: String,
            required: true
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {
        const list = computed( () => filterAffiliations(props.modelValue, props.type))
        const isEmpty = computed(() => _.isEmpty(list.value))

        function filterAffiliations (affiliations, type) {
            return _.filter(affiliations, {type: type})
        }

        const removeEntity = function (entity) {
            emit('update:modelValue', _.filter(props.modelValue, (entry) => entry !== entity))
        }

        return {
            isEmpty,
            list,
            removeEntity
        }
    },
    methods: {


    },
}
</script>

<style scoped>

</style>
