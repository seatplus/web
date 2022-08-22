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
                v-model="search"
                class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              >
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <Multiselect
                v-model="regions"
                route="autosuggestion.region"
                label="Region"
                placeholder="search for region"
              />
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <Multiselect
                v-model="systems"
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
import {computed, ref, watch} from 'vue'
import { SwitchGroup, Switch, SwitchLabel } from '@headlessui/vue'
import SelectedEntity from "@/Shared/Components/SelectedEntity";
import route from 'ziggy'

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
        characterIds: {
            required: true,
            type: Array,
            default: () => []
        },
    },
    setup(props) {
        const switchValue = ref(false)
        const infiniteId = ref(+new Date())
        const search = ref(null)
        const regions = ref([])
        const systems = ref([])

        /*const selectedCharacterIds = computed(() => {
            let character_ids = _.get(route().params, 'character_ids')

            if(!character_ids)
                return []

            return  _.map(character_ids, (id) => parseInt(id))
        })*/

        const cleanParams = computed(() => {
            return {
                search: search.value === "" ? null : search.value,
                character_ids: props.characterIds,
                regions: _.map(regions.value, 'id'),
                systems: _.map(systems.value, 'id')
            }
        })

        const debounceInfiniteId = _.debounce(() => infiniteId.value++, 250)

        watch(() => search.value, (newValue) => {
            if(_.size(newValue)>=3) {
                debounceInfiniteId()
            }
        })

        watch([regions.value, systems.value], () => infiniteId.value++)

        return {
            infiniteId,
            search,
            regions,
            systems,
            switchValue,
            cleanParams,
            pageTitle: 'Character Assets',
        }
    }
}
</script>

<style scoped>

</style>
