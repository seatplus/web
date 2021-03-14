<template>

  <PageHeader>
    Character Assets
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
            <!--            <Autosuggest
                          v-model="params.regions"
                          route="autosuggestion.region"
                          label="Region"
                          placeholder="search for region"
                        />-->
          </div>

          <div class="col-span-6 md:col-span-3 lg:col-span-2">
            <!--            <Autosuggest
                          v-model="params.systems"
                          route="autosuggestion.system"
                          label="Solar System"
                          placeholder="search for solar system"
                        />-->
          </div>
        </div>
      </div>
    </div>

    <AssetsComponent
      :url="url"
      @openManualLocationModal="openModal = true, modal_location_id = $event"
      :key="url"
    />

    <!--    <template #modal>
          <AddManualLocationModal
            v-model="openModal"
            :location_id="modal_location_id"
          />
        </template>-->
  </div>
</template>

<script>
//import Layout from "../../Shared/Layout"
import PageHeader from "../../Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import CharacterSelectionButton from "@/Shared/Components/SlideOver/CharacterSelectionButton";
import AddManualLocationModal from "../../Shared/Components/Assets/AddManualLocationModal";
import LocationComponent from "@/Shared/Components/Assets/LocationComponent";
import AssetsComponent from "@/Shared/Components/Assets/AssetsComponent";
import Layout from "@/Shared/SidebarLayout/Layout";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton";

export default {
  name: "Assets",
  components: {
    DispatchUpdateButton,
    AssetsComponent,
    LocationComponent,
    //Autosuggest,
    AddManualLocationModal,
    CharacterSelectionButton,
    PageHeader
    //Layout
  },

  props: {
    dispatch_transfer_object: {
      required: true,
      type: Object,
      default: () => {}
    },
  },
  layout: (h, page) => h(Layout, { dispatch_transfer_object: page.props.dispatch_transfer_object }, [page]),
  data() {
    return {
      params: {
        character_ids: this.selectedCharacterIds,
        search: null,
        regions: null,
        systems: null,
      },
      openModal: false,
      modal_location_id: 0
    }
  },
  computed: {
    url() {
      return this.$route('load.character.assets',this.cleanParams)
    },
    cleanParams() {
      let params = this.params

      params.search = params.search === "" ? null : params.search

      return params
    }
  },
  methods: {
    openSlideOver(value) {
      //TODO this.$eventBus.$emit('open-slideOver', value);
    },
  }
}
</script>

<style scoped>

</style>
