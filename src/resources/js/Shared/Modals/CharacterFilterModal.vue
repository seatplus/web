<template>
    <Modal v-model="openModal">
        <div  v-if="openModal" class="bg-white rounded-lg px-4 pt-5 pb-4 max-h-3/4 overflow-hidden shadow-xl transform transition-all sm:max-w-3xl sm:w-full sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div :class="[{'h20': characters.length < 4, 'h40': 3 < characters.length < 7, 'h-64': 6 < characters.length},'overflow-y-auto']">
                <ul class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 my-6 sm:my-5">
                    <li :key="character.character_id" v-for="character of [...ownedCharacters, ...affiliatedCharacters]" @click="flipSelect(character)" class="col-span-1 bg-white rounded-lg shadow">
                        <div class="w-full flex items-center justify-between p-3 space-x-3">
                            <div class="flex-1 truncate">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-gray-900 text-sm leading-5 font-medium truncate"> {{ character.name }}</h3>
                                    <span v-if="isSelected(character)" class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Selected</span>
                                </div>
                                <p class="mt-1 text-gray-500 text-sm leading-5 truncate"> {{ character.owned_by_user ? 'owned' : 'affiliated'}} </p>
                            </div>
                            <EveImage :object="character" :size="256" tailwind_class="w-8 h-8 bg-gray-300 rounded-full flex-shrink-0" />
                        </div>
                    </li>
                </ul>
            </div>
            <div class="mt-5 sm:mt-6">
                <span class="flex w-full rounded-md shadow-sm">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Go back to last view
                    </button>
                </span>
            </div>
        </div>
    </Modal>
</template>

<script>
  import EveImage from "../EveImage"
  import Modal from "./Modal"
  export default {
      name: "CharacterFilterModal",
      components: {Modal, EveImage},
      props: {
          value: {},
          permission: {
              required: true,
              type: String
          },
      },
      data() {
          return {
              openModal: this.value.open,
              selected: this.value.selectedCharacters,
              characters: [],
              route: this.$route('get.affiliated.characters', this.permission),
              page: 1
          }
      },
      methods: {
          flipSelect(entity) {

              let index = this.selected.indexOf(entity.character_id)

              if(index >= 0)
                  return this.removeSelected(entity)

              this.selected.push(entity.character_id)
          },
          isSelected(entity) {
              return this.selected.includes(entity.character_id)
          },
          removeSelected(entity) {
              this.selected = _.remove(this.selected, (select) => select !== entity.character_id)
          },
          load: function () {
              axios.get(this.route, {
                  params: {
                      page: this.page,
                  },
              }).then(({data}) => {
                  if (data.data.length) {
                      this.page += 1
                      this.characters.push(...data.data)
                      this.load()
                  }
              }).catch(error => {
                  console.log(error)
              })
          },
      },
      mounted() {
          this.load();
      },
      computed: {
          open() {
              return this.value.open
          },
          ownedCharacters() {
              return _.filter(this.characters, character => character.owned_by_user)
          },
          affiliatedCharacters() {
              return _.filter(this.characters, character => !character.owned_by_user)
          }
      },
      watch: {
          openModal(newVal) {
              this.$emit('input', {
                  open: newVal,
                  selectedCharacters: this.selected
              })

          },
          selected(newVal) {

              this.$emit('input', {
                  open: this.openModal,
                  selectedCharacters: newVal
              })
          },
          open(newVal) {
              this.openModal = newVal
          }
      }
  }
</script>

<style scoped>

</style>
