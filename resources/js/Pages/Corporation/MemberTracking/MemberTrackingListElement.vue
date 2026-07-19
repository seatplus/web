<template>
  <li
    :class="[even ? 'bg-gray-50' : 'bg-white']"
    class="hidden sm:grid grid-cols-12 gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-3 py-4 sm:py-1 self-center whitespace-normal col-span-4">
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
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import Time from "@/Shared/Time.vue";
export default {
    name: "MemberTrackingListElement",
    components: {Time, EntityByIdBlock, EntityBlock},
    props: {
        member: {
            required: true,
            type: Object
        },
        even: {
            required: true,
            type: Number
        }
    },
    computed: {
        locationName() {
            return _.get(this.member, 'location.name', 'Unknown Location')
        }
    }
}
</script>

<style scoped>

</style>