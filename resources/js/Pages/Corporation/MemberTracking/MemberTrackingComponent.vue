<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="flex-grow"
          :entity="corporation"
        />
      </div>
    </template>

    <div class="relative max-h-96 overflow-y-auto">
      <div class="hidden sm:grid grid-cols-12 gap-x-0 gap-y-1 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-3 py-1">
          Token
        </div>
        <div class="px-3 py-1 col-span-3">
          Name
        </div>
        <div class="px-3 py-1 col-span-3">
          Last Location
        </div>
        <div class="px-3 py-1 col-span-3">
          Ship
        </div>
        <div class="px-3 py-1">
          Joined
        </div>
        <div class="px-3 py-1">
          Last Login
        </div>
      </div>

      <ul class="relative z-0">
        <MemberTrackingListElementForSmallDevices
          v-for="member in result"
          :key="member.character_id"
          :member="member"
          :required_scopes="corporation.required_scopes"
        />
        <MemberTrackingListElement
          v-for="(member, index) in result"
          :key="member.character_id"
          :member="member"
          :required_scopes="corporation.required_scopes"
          :even="index%2"
        />
        <div ref="scrollComponent" />
      </ul>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import MemberTrackingListElement from "./MemberTrackingListElement.vue";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
import MemberTrackingListElementForSmallDevices from "./MemberTrackingListElementForSmallDevices.vue";

export default {
    name: "MemberTrackingComponent",
    components: {
        MemberTrackingListElementForSmallDevices,
        MemberTrackingListElement, EntityBlock, CardWithHeader
    },
    props: {
        corporation: {
            required: true,
            type: Object
        },
    },
    setup(props) {

        return useInfinityScrolling('get.corporation.member_tracking', props.corporation.corporation_id)
    }
}
</script>

<style scoped>

</style>
