<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />

    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <EntitySelectionButton />
      </template>
    </PageHeader>

    <div>
      <div class="bg-white overflow-hidden shadow-lg rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-6 gap-5">
            <div class="col-span-6 lg:col-span-2">
              <label
                for="search"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Search
              </label>
              <input
                id="search"
                v-model="params.search"
                class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              >
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <Multiselect
                v-model="params.regions"
                route="autosuggestion.region"
                label="Region"
                placeholder="search for region"
              />
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <Multiselect
                v-model="params.systems"
                route="autosuggestion.system"
                label="Solar System"
                placeholder="search for solar system"
              />
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <div class="w-full max-w-xs">
                <SwitchGroup
                  as="div"
                  class="flex items-center space-x-4"
                >
                  <SwitchLabel>Compact view</SwitchLabel>

                  <Switch
                    v-slot="{ checked }"
                    v-model="switchValue"
                    as="button"
                    class="relative inline-flex flex-shrink-0 h-6 transition-colors duration-200 ease-in-out border-2 border-transparent rounded-full cursor-pointer w-11 focus:outline-none focus:shadow-outline"
                    :class="switchValue ? 'bg-indigo-600' : 'bg-gray-200'"
                  >
                    <span
                      class="inline-block w-5 h-5 transition duration-200 ease-in-out transform bg-white rounded-full"
                      :class="{ 'translate-x-5': checked, 'translate-x-0': !checked }"
                    />
                  </Switch>
                </SwitchGroup>
              </div>
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-4">
              <SelectedEntity />
            </div>
          </div>
        </div>
      </div>

      <AssetsComponent
        :key="infiniteId"
        :parameters="cleanParams"
        :compact="switchValue"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import Multiselect from "@/Shared/Components/Multiselect";
import { ref } from 'vue'
import { SwitchGroup, Switch, SwitchLabel } from '@headlessui/vue'
import SelectedEntity from "../../Shared/Components/SelectedEntity";

export default {
    name: "Assets",
    components: {
        SelectedEntity,
        Multiselect,
        RequiredScopesWarning,
        DispatchUpdateButton,
        AssetsComponent,
        EntitySelectionButton,
        PageHeader,
        Switch,
        SwitchGroup,
        SwitchLabel
    },
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object,
            default: () => {}
        },
    },
    setup() {
        const switchValue = ref(false)

        return {
            switchValue,
        }
    },
    data() {
        return {
            pageTitle: 'Character Assets',
            infiniteId: +new Date(),
            params: {
                character_ids: this.selectedCharacterIds,
                search: null,
                regions: [],
                systems: [],
            },
        }
    },
    computed: {
        cleanParams() {
            let params = this.params

            return {
                search: params.search === "" ? null : params.search,
                character_ids: this.selectedCharacterIds,
                regions: _.map(params.regions, 'id'),
                systems: _.map(params.systems, 'id')
            }
        },
        selectedCharacterIds() {

            let character_ids = _.get(this.$route().params, 'character_ids')

            if(!character_ids)
                return []

            return  _.map(character_ids, (id) => parseInt(id))
        }
    },
    watch: {
        params: {
            deep: true,
            handler() {
                this.infiniteId += 1
            }
        }
    },
}
</script>

<style scoped>

</style>
