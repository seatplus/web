<template>
  <!-- Be sure to use this with a layout container that is full-width on mobile -->
  <div class="bg-white overflow-hidden shadow sm:rounded-lg my-5">
    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
      <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
        <!--<div class="ml-4 mt-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <EveImage
                tailwind_class="h-12 w-12 rounded-full"
                :size="256"
                :object="corporation"
              />
            </div>
            <div class="ml-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ corporation.name }}
              </h3>
              <p class="text-sm leading-5 text-gray-500">
                {{ corporation.alliance ? corporation.alliance.name : '' }}
              </p>
            </div>
          </div>
        </div>-->
        <div class="ml-4 mt-4">
          <EntityBlock :entity="enlistment.corporation" />
        </div>

        <div
          v-if="enlistment.can_manage"
          class="ml-4 mt-4 flex-shrink-0 flex space-x-3"
        >
          <span class="inline-flex rounded-md shadow-sm">

            <Link
              :href="$route('edit.enlistment', enlistment.corporation_id)"
              method="get"
              as="button"
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:border-indigo-300 focus:ring-offset-2 focus:ring-indigo active:bg-indigo-200"
            >
              Edit Enlistment
            </Link>
          </span>
        </div>
      </div>
    </div>

    <div class="px-4 py-5 sm:p-6 space-y-4 sm:space-y-6">
      <!--TODO: add finished applications-->
      <BarWithUnderline
        v-if="steps.length > 1"
        :tabs="steps"
        @select="changeActiveTab"
      />

      <div class="bg-white overflow-hidden shadow sm:rounded-lg relative max-h-96 overflow-y-auto">
        <PendingTable
          v-if="isPending"
          :step-count="stepIndex"
          :corporation-id="enlistment.corporation_id"
        />
        <ClosedTable
          v-else
          :corporation-id="enlistment.corporation_id"
        />
      </div>
    </div>
  </div>
</template>

<script>

import {Link} from "@inertiajs/inertia-vue3";
import EntityBlock from "../../../Shared/Layout/Eve/EntityBlock";
import BarWithUnderline from "../../../Shared/Layout/Tabs/BarWithUnderline";
import PendingTable from "./ApplicationsTable/PendingTable";
import ClosedTable from "./ApplicationsTable/ClosedTable";

export default {
    name: "CorporationRecruitment",
    components: {
        ClosedTable,
        PendingTable,
        BarWithUnderline, EntityBlock, Link },
    props: {
        enlistment: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            stepIndex: 0,
            headerTitles: [
                {title: 'Main Character', columnSpan: 3},
                {title: 'Characters', columnSpan: 7},
                {title: 'Review', columnSpan: 2, srOnly: true},
            ],
            rawPending: []
        }
    },
  computed: {
      steps() {
          let steps = _.map(this.enlistment.steps, (value, index) => new Object({id: index, name: value}))

          steps.push({
              id: this.enlistment.steps.length+1,
              name: 'Closed'
          })

          return steps
      },
      pending() {
          return _.filter(this.rawPending, {decision_count: this.stepIndex})
      },
      isPending() {
          return this.enlistment.steps.length >= this.stepIndex+1
      }
  },
    methods: {
        changeActiveTab(activeTab) {
            this.stepIndex = activeTab.id
        }
    }
}
</script>

<style scoped>

</style>
