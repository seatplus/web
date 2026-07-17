<template>
  <li class="hidden sm:block">
    <StickyHeaderTableRow :number-columns="countColumns">
      <StickyHeaderCell
        :cell="columns[0]"
        class="self-center"
      >
        <CheckCircleIcon
          v-if="isOk"
          class="h-5 w-5 text-emerald-500"
        />
        <XCircleIcon
          v-else
          class="h-5 w-5 text-red-500"
        />
      </StickyHeaderCell>

      <StickyHeaderCell
        :cell="columns[1]"
        class="self-center"
      >
        <EntityBlock
          v-if="member.character"
          :entity="member.character"
          :image-size="8"
          name-font-size="md"
        />
        <EntityByIdBlock
          v-else
          :id="member.character_id"
          :with-sub-text="false"
          :image-size="8"
          name-font-size="md"
        />
      </StickyHeaderCell>

      <StickyHeaderCell
        :cell="columns[2]"
        class="self-center"
      >
        {{ locationName }}
      </StickyHeaderCell>

      <StickyHeaderCell
        :cell="columns[3]"
        class="self-center"
      >
        <EntityBlock
          :entity="{type_id: member.ship_type_id, name: member.ship?.name}"
          :image-size="6"
          name-font-size="sm"
        />
      </StickyHeaderCell>

      <StickyHeaderCell
        :cell="columns[4]"
        class="self-center"
      >
        <Time
          format="YYYY-MM-DD HH:mm:ss"
          :timestamp="member.start_date"
        />
      </StickyHeaderCell>

      <StickyHeaderCell
        :cell="columns[5]"
        class="self-center"
      >
        <Time
          format="YYYY-MM-DD HH:mm:ss"
          :timestamp="member.logon_date"
        />
      </StickyHeaderCell>
    </StickyHeaderTableRow>
  </li>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/20/solid'
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import Time from "@/Shared/Time.vue";
import StickyHeaderTableRow from "@/Shared/Layout/Table/StickyHeaderTableRow.vue";
import StickyHeaderCell from "@/Shared/Layout/Table/StickyHeaderCell.vue";

export default {
    name: "MemberTrackingListElement",
    components: {Time, EntityByIdBlock, EntityBlock, CheckCircleIcon, XCircleIcon, StickyHeaderTableRow, StickyHeaderCell},
    props: {
        member: {
            required: true,
            type: Object
        },
        columns: {
            required: true,
            type: Array
        },
        countColumns: {
            required: true,
            type: Number
        },
        required_scopes: {
            required: true,
            type: Array
        }
    },
    computed: {
        missing_scopes() {

            return _.differenceWith(this.required_scopes, this.refresh_token_scopes, _.isEqual)
        },
        refresh_token_scopes() {
            return _.get(this.member, 'character.refresh_token.scopes', [])
        },
        isOk() {
            return _.isEmpty(this.missing_scopes) && !_.isEmpty(this.refresh_token_scopes)
        },
        locationName() {
            return _.get(this.member, 'location.name', 'Unknown Location')
        }
    }
}
</script>

<style scoped>

</style>
