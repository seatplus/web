<template>
  <StickyHeaderTable :header-titles="headerTitles">
    <template #default="{ countColumns, columns }">
      <StickyHeaderTableRow
        v-for="applicant in applications"
        :key="applicant"
        :number-columns="countColumns"
      >
        <StickyHeaderCell
          :cell="columns[0]"
          class="sm:flex truncate"
        >
          <div class="flex-shrink self-center">
            <EntityByIdBlock
              :id="applicant.main_character.character_id"
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
              :key="character"
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
                :href="$route('get.application', applicant.application_id)"
              >
                Review
              </Button>
            </div>
          </slot>
        </stickyheadercell>
      </StickyHeaderTableRow>
    </template>
  </StickyHeaderTable>
</template>

<script>
import StickyHeaderTable from "@/Shared/Layout/Table/StickyHeaderTable";
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import CharacterComplianceElement from "../../MemberCompliance/CharacterComplianceElement";
import Button from "@/Shared/Layout/Button";

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
        }
    },
    setup() {
        return {
            headerTitles
        }
    }
}
</script>

<style scoped>

</style>