<template>
  <span class="inline-block relative">
    <HeaderButton
      :secondary="true"
      @click="openSlideOver"
    >
      Select {{ type.replace(/^\w/, c => c.toUpperCase()) }}
    </HeaderButton>
    <span
      v-if="has_selected"
      class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-yellow-400"
    />
  </span>
  <teleport to="#destination">
    <SlideOver v-model:open="open">
      <template #title>
        Select {{ type }}
      </template>
      <div class="relative overflow-y-auto">
        <div class="z-10 sticky top-0">
          <div class="ml-3 mb-3 relative rounded-md bg-white shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <SearchIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <input
              id="search"
              v-model="search"
              type="search"
              name="search"
              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
              :placeholder="`${type} name`"
            >
          </div>
        </div>
        <EntitySelection
          :dispatch-transfer-object="dispatchTransferObject"
          :type="type"
          :search="search"
        />
      </div>
    </SlideOver>
  </teleport>
</template>

<script>
import HeaderButton from "../../Layout/HeaderButton";
import SlideOver from "../../Layout/SlideOver";
import EntitySelection from "./EntitySelection";
import { SearchIcon } from '@heroicons/vue/solid';

export default {
    name: "EntitySelectionButton",
    components: {EntitySelection, SlideOver, HeaderButton, SearchIcon},
    props: {
        type: {
            type: String,
            default: () => 'character'
        }
    },
    data() {
        return {
            open: false,
            search: ''
        }
    },
    computed: {
        has_selected() {
            let ids = _.get(this.route().params, `${this.type}_ids`)

            return !!ids
        },
        dispatchTransferObject() {
            return this.$page.props.dispatch_transfer_object != null ? this.$page.props.dispatch_transfer_object : this.$page.props.dispatchTransferObject
        }
    },
    methods: {
        openSlideOver() {
            this.open = true
        },
    },
}
</script>

<style scoped>

</style>
