<template>
  <li
    :class="[even ? 'bg-gray-50' : 'bg-white']"
    class="hidden sm:grid grid-cols-12 gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal">
      <CheckCircleIcon
        v-if="isOk"
        class="h-5 w-5 text-green-500"
      />
      <XCircleIcon
        v-else
        class="h-5 w-5 text-red-500"
      />
    </div>

    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal col-span-3">
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
    </div>

    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal col-span-3">
      {{ locationName }}
    </div>

    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal col-span-3">
      <EntityBlock
        :entity="{type_id: member.ship_type_id, name: member.ship?.name}"
        :image-size="6"
        name-font-size="sm"
      />
    </div>
    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal">
      <Time
        format="YYYY-MM-DD HH:mm:ss"
        :timestamp="member.start_date"
      />
    </div>

    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal">
      <Time
        format="YYYY-MM-DD HH:mm:ss"
        :timestamp="member.logon_date"
      />
    </div>
  </li>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/solid'
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import Time from "@/Shared/Time";
export default {
    name: "MemberTrackingListElement",
    components: {Time, EntityByIdBlock, EntityBlock, CheckCircleIcon, XCircleIcon},
    props: {
        member: {
            required: true,
            type: Object
        },
        even: {
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