<template>
  <CardWithHeader class="my-5">
    <template #header>
      <div class="flex flex-wrap items-center justify-between gap-4 sm:flex-nowrap">
        <EntityBlock :entity="enlistment.corporation" />

        <Button
          v-if="enlistment.can_manage"
          :href="editEnlistment.url({ corporation_id: enlistment.corporation_id })"
          method="get"
          class="shrink-0"
        >
          Edit Enlistment
        </Button>
      </div>
    </template>

    <div class="px-4 py-5 sm:p-6 space-y-4 sm:space-y-6">
      <!--TODO: add finished applications-->
      <BarWithUnderline
        v-if="steps.length > 1"
        :tabs="steps"
        @select="changeActiveTab"
      />

      <div class="relative max-h-96 overflow-y-auto rounded-lg">
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
  </CardWithHeader>
</template>

<script>

import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import Button from "@/Shared/Layout/Button.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import BarWithUnderline from "@/Shared/Layout/Tabs/BarWithUnderline.vue";
import PendingTable from "./ApplicationsTable/PendingTable.vue";
import ClosedTable from "./ApplicationsTable/ClosedTable.vue";
import { edit as editEnlistment } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/EnlistmentsController";

export default {
    name: "CorporationRecruitment",
    components: {
        CardWithHeader,
        Button,
        ClosedTable,
        PendingTable,
        BarWithUnderline, EntityBlock },
    props: {
        enlistment: {
            required: true,
            type: Object
        },
    },
    setup() {
        return { editEnlistment };
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
