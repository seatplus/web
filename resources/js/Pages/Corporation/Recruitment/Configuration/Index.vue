<template>
  <div>
    <PageHeader :breadcrumbs="breadcrumbs">
      Corporation Enlistment
      <template #primary>
        <!--TODO: Create Delete Button with confirmation dialog-->
        <span class="shadow-xs rounded-md">
          <Button
            :href="deleteUrl"
            method="delete"
          >
            Delete
          </Button>
        </span>
      </template>
    </PageHeader>

    <EnlistmentConfig
      class="pt-6 sm:pt-10"
      :with-bottom-border="true"
      :enlistment="enlistment"
    />

    <TwoColumnCardWithSubmitAction :index="0">
      <template #title>
        Region or System Filter
      </template>

      <template #description>
        Please setup your region or system filter. Items, contracts within the selected regions, systems etc. will show up during recruitment
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <EsiMultiselect
            v-model="form.regions"
            :categories="['region']"
            label="Region"
            placeholder="search for region"
          />
        </div>

        <div>
          <EsiMultiselect
            v-model="form.systems"
            :categories="['solar_system']"
            label="Solar System"
            placeholder="search for solar system"
          />
        </div>
      </div>


      <template #button>
        <Button
          :is-inertia-button="false"
          @click="submit"
        >
          Save
        </Button>
      </template>
    </TwoColumnCardWithSubmitAction>
    <ItemsWatchlist
      :corporation-id="enlistment.corporation_id"
      :items="watched.items"
    />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction.vue";
import EnlistmentConfig from "./EnlistmentConfig.vue";
import Button from "@/Shared/Layout/Button.vue";
import ItemsWatchlist from "./ItemsWatchlist.vue";
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import { useForm } from "@inertiajs/vue3";
import { deleteMethod as deleteEnlistment, updateWatchlist } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/EnlistmentsController";
import GetRecruitmentIndexController from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController";

export default {
    name: "Index",
    components: {ItemsWatchlist, Button, EnlistmentConfig, EsiMultiselect, TwoColumnCardWithSubmitAction, PageHeader},
    props: {
        watched: {
            required: true,
            type: Object
        },
        enlistment: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            breadcrumbs: [
                {
                    name: 'Corporation Recruitment',
                    route: GetRecruitmentIndexController.url()
                }
            ],
            form: useForm({
                systems: this.watched.systems,
                regions: this.watched.regions
            }),
        }
    },
    computed: {
        deleteUrl() {
            return deleteEnlistment.url(this.enlistment.corporation_id)
        }
    },
    methods: {
        submit() {
            this.form.post(updateWatchlist.url(this.enlistment.corporation_id))
        }
    }
}
</script>

<style scoped>

</style>
