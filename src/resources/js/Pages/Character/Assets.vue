<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <RequiredScopesWarning :dispatch_transfer_object="dispatch_transfer_object" />

    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <CharacterSelectionButton />
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
              >Search</label>
              <input
                id="search"
                v-model="params.search"
                class="mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              >
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
              <!--                        <SelectComponent v-model="params.region" :options="regions">Region Filter</SelectComponent>-->
              <!--              <Autosuggest
                route="autosuggestion.region"
                label="Region"
                placeholder="search for region"
                @selected="selectRegion"
              />-->
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
          </div>
        </div>
      </div>

      <AssetsComponent
        :key="infiniteId"
        :parameters="cleanParams"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";
import Autosuggest from "@/Shared/Components/Autosuggest";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning";
import Multiselect from "@/Shared/Components/Multiselect";

export default {
  name: "Assets",
  components: {
    Multiselect,
    RequiredScopesWarning,
    Autosuggest,
    DispatchUpdateButton,
    AssetsComponent,
    CharacterSelectionButton,
    PageHeader
  },
  props: {
    dispatch_transfer_object: {
      required: true,
      type: Object,
      default: () => {}
    },
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

      params.search = params.search === "" ? null : params.search

      return params
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
  methods: {
    selectRegion(selected_id) {
      this.params.regions = selected_id
      this.infiniteId++
    },
    selectSystem(selected_id) {
      this.params.systems = selected_id
      this.infiniteId++
    }
  }
}
</script>

<style scoped>

</style>
