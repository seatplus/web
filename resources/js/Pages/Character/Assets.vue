<template>
  <div class="space-y-3">
    <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />

    <PageHeader :page-title="pageTitle">
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
                class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-xs focus:outline-hidden focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              >
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <EsiMultiselect
                v-model="regions"
                :categories="['region']"
                label="Region"
                placeholder="search for region"
              />
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <EsiMultiselect
                v-model="systems"
                :categories="['solar_system']"
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
                    class="relative inline-flex shrink-0 h-6 transition-colors duration-200 ease-in-out border-2 border-transparent rounded-full cursor-pointer w-11 focus:outline-hidden focus:shadow-outline"
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

      <AssetsComponent :compact="switchValue" />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import {computed, ref, watch} from 'vue'
import { router } from "@inertiajs/vue3";
import { SwitchGroup, Switch, SwitchLabel } from '@headlessui/vue'
import SelectedEntity from "@/Shared/Components/SelectedEntity.vue";
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import { ls } from "@/Functions/useLocalStorage";

// Remember the compact/wide choice across visits (persisted for a year, refreshed on each toggle).
const COMPACT_VIEW_KEY = 'assets.compactView'
const COMPACT_VIEW_TTL = 365 * 24 * 60 * 60 * 1000

export default {
    name: "Assets",
    components: {
        EsiMultiselect,
        SelectedEntity,
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
        const switchValue = ref(ls.get(COMPACT_VIEW_KEY) ?? false)
        const search = ref(null)
        const regions = ref([])
        const systems = ref([])

        const cleanParams = computed(() => {
            return {
                search: search.value === "" ? null : search.value,
                character_ids: props.characterIds,
                regions: _.map(regions.value, 'id'),
                systems: _.map(systems.value, 'id')
            }
        })

        // Reload only the `assets` scroll prop with the current filters; reset so
        // <InfiniteScroll> replaces the list with the filtered first page instead of merging.
        const reload = () => router.reload({
            only: ['assets'],
            reset: ['assets'],
            data: cleanParams.value,
            preserveState: true,
            preserveScroll: true,
        })

        const debouncedReload = _.debounce(reload, 350)

        // Search reloads on 3+ chars or when cleared; region/system selections reload immediately.
        watch(search, (newValue) => {
            if (! newValue || _.size(newValue) >= 3) {
                debouncedReload()
            }
        })

        watch([regions, systems], () => reload(), { deep: true })

        watch(switchValue, (value) => ls.set(COMPACT_VIEW_KEY, value, COMPACT_VIEW_TTL))

        return {
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
