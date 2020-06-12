<template>
    <div :class="['bg-white sm:rounded-md', {'shadow' : !isEmpty(list)}]">
        <ListTransition :entries="list" :class="'divide-y divide-grey-200'">
            <div v-for="(entity, index) of list" :key="index" class="px-4 py-4 flex items-center justify-between sm:px-6 block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                <EveImage v-if="entity" :object="entity" :size="128" tailwind_class="h-12 w-12 rounded-full" show-name />
                <button @click="removeEntity(entity)" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:bg-green-100 transition ease-in-out duration-150">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </ListTransition>
    </div>
</template>

<script>
  import ListTransition from "../../Shared/Transitions/ListTransition"
  import EveImage from "../../Shared/EveImage"
  export default {
      name: "AffiliationList",
      components: {EveImage, ListTransition},
      props: ['value'],
      data() {
          return {
              list: []
          }
      },
      methods: {
          isEmpty(array) {
              return _.isEmpty(array)
          },
          removeEntity(entity) {
              this.list = _.filter(this.list, (entry) => entry !== entity)
          }
      },
      watch: {
          list(newValue) {
              this.$emit('input', newValue)
          },
          value(newValue) {
              this.list = newValue
          }
      },
  }
</script>

<style scoped>

</style>
