<template>
  <StickyHeaderTable
    :header-titles="headerTitles"
    :body-id="bodyId"
  >
    <template #default="{ countColumns, columns }">
      <StickyHeaderTableRow
        v-for="applicant in applications"
        :key="applicant.application_id"
        :number-columns="countColumns"
      >
        <StickyHeaderCell
          :cell="columns[0]"
          class="sm:flex truncate"
        >
          <div class="shrink self-center">
            <EntityByIdBlock
              :id="applicant.mainCharacter.character_id"
              class="flex gap-4 truncate"
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
              :key="character.character_id"
              :character="character"
            />
          </div>
        </StickyHeaderCell>

        <StickyHeaderCell
          :cell="columns[2]"
          class="self-center"
        >
          <slot :applicant="applicant">
            <div class="flex justify-end">
              <Button
                button-size="xs"
                :href="getApplication.url(applicant.application_id)"
              >
                Review
              </Button>
            </div>
          </slot>
        </StickyHeaderCell>
      </StickyHeaderTableRow>
    </template>
  </StickyHeaderTable>
</template>

<script>
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable.vue";
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow.vue";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import CharacterComplianceElement from "@/Pages/Corporation/MemberCompliance/CharacterComplianceElement.vue";
import Button from "@/Shared/Layout/Button.vue";
import { getApplication } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/ApplicationsController";

let headerTitles = [
    {title: 'Main Character', columnSpan: 3},
    {title: 'Characters', columnSpan: 7},
    {title: 'Review', columnSpan: 2, srOnly: true},
];

export default {
    name: "ApplicationsTable",
    components: {
        Button,
        CharacterComplianceElement, EntityByIdBlock, StickyHeaderCell, StickyHeaderTableRow, StickyHeaderTable},
    props: {
        applications: {
            required: true,
            type: Array
        },
        // Optional id for the row <ul> so an ancestor <InfiniteScroll items-element> can target it.
        bodyId: {
            required: false,
            type: String,
            default: null
        }
    },
    setup() {
        return {
            headerTitles,
            getApplication
        }
    }
}
</script>

<style scoped>

</style>
