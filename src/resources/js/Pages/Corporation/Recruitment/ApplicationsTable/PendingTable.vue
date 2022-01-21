<template>
  <InfiniteLoadingHelper
    route="open.corporation.applications"
    :params="{corporation_id: corporationId}"
    @result="(results) => raw_pending = results"
  >
    <StickyHeaderTable :header-titles="headerTitles">
      <template #default="{ countColumns, columns }">
        <StickyHeaderTableRow
          v-for="applicant in pending"
          :key="applicant"
          :number-columns="countColumns"
        >
          <StickyHeaderCell :cell="columns[0]">
            <div class="flex-shrink self-center">
              <EntityByIdBlock
                :id="applicant.main_character.character_id"
                class="truncate"
                :image-size="10"
              />
            </div>
          </StickyHeaderCell>

          <StickyHeaderCell
            :cell="columns[1]"
            class="self-center"
          >
            <div class="flex gap-x-2 flex-wrap">
              <CharacterComplianceElement
                v-for="character in applicant.characters"
                :key="character"
                :character="character"
              />
            </div>
          </StickyHeaderCell>

          <StickyHeaderCell
            :cell="columns[2]"
            class="self-center"
          >
            <div class="flex justify-end">
              <Button
                button-size="xs"
                :href="$route('get.application', applicant.application_id)"
              >
                Review
              </Button>
            </div>
          </stickyheadercell>
        </StickyHeaderTableRow>
      </template>
    </StickyHeaderTable>
  </InfiniteLoadingHelper>
</template>

<script>
import InfiniteLoadingHelper from "@/Shared/InfiniteLoadingHelper";
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable";
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell";
import CharacterComplianceElement from "@/Pages/Corporation/MemberCompliance/CharacterComplianceElement";
import {computed, ref} from "vue";
import Button from "@/Shared/Layout/Button";

let headerTitles = [
    {title: 'Main Character', columnSpan: 3},
    {title: 'Characters', columnSpan: 7},
    {title: 'Review', columnSpan: 2, srOnly: true},
];

export default {
    name: "PendingTable",
    components: {
        Button,
        CharacterComplianceElement,
        StickyHeaderCell, EntityByIdBlock, StickyHeaderTableRow, StickyHeaderTable, InfiniteLoadingHelper},
    props: {
        stepCount: {
            required: true,
            type: Number
        },
        corporationId: {
            required: true,
            type: Number
        }
    },
    setup(props) {

        const raw_pending = ref([])

        const pending = computed(() => _.filter(raw_pending.value, {decision_count: props.stepCount}))

        return {
            raw_pending,
            pending,
            headerTitles
        }
    }
}
</script>

<style scoped>

</style>