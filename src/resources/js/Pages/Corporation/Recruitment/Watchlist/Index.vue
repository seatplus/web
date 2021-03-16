<template>
  <div>
    <PageHeader :breadcrumbs="breadcrumbs">
      Corporation Watchlist
    </PageHeader>

    <TwoColumnCardWithSubmitAction :index="0">
      <template #title>
        Filter
      </template>

      <template #description>
        Please setup your watchlist filter. Items, contracts within the selected regions, systems etc. will show up during recruitment
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <Multiselect
            v-model="form.regions"
            route="autosuggestion.region"
            label="Region"
            placeholder="search for Region"
          />
        </div>

        <div>
          <Multiselect
            v-model="form.systems"
            route="autosuggestion.system"
            label="Solar System"
            placeholder="search for solar system"
          />
        </div>
      </div>

      <template #button>
        <button
          :disabled="form.processing"
          type="submit"
          class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="submit"
        >
          Save
        </button>
      </template>
    </TwoColumnCardWithSubmitAction>
  </div>
</template>

<script>
import Layout from "@/Shared/SidebarLayout/Layout";
import PageHeader from "@/Shared/Layout/PageHeader";
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction";
import Autosuggest from "@/Shared/Components/Autosuggest";
import Multiselect from "@/Shared/Components/Multiselect";

export default {
  name: "Index",
  components: {Multiselect, TwoColumnCardWithSubmitAction, PageHeader},
  props: {
    corporation_id: {
      required: true,
      type: Number
    },
    watched_systems: {
      required: true,
      type: Array
    },
    watched_regions: {
      required: true,
      type: Array
    },
  },
  layout: (h, page) => h(Layout, { activeSidebarElement: page.props.activeSidebarElement }, [page]),
  data() {
    return {
      breadcrumbs: [
        {
          name: 'Corporation Recruitment',
          route: this.$route('corporation.recruitment')
        }
      ],
      form: this.$inertia.form({
        systems: this.watched_systems,
        regions: this.watched_regions
      }),
    }
  },
  watch: {
    selected_system:Autosuggest.vue
  },
  methods: {
    submit() {
      this.$inertia.post(this.$route('update.watchlist', this.corporation_id), this.form)
    }
  }
}
</script>

<style scoped>

</style>
