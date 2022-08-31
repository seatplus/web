<template>
  <li
    :class="even ? (has_standing_offset ? 'bg-yellow-100' :'bg-gray-50') : (has_standing_offset ? 'bg-yellow-50' :'bg-white')"
    class="grid grid-cols-2 sm:grid-cols-6 sm:gap-x-0 sm:gap-y-1 grid-flow-row text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Contact
      </label>
      <EntityByIdBlock
        :id="entry.contact_id"
        :image-size="10"
        name-font-size="text-base"
      />
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Labels
      </label>
      <div class="ml-2 flex space-x-1 space-y-1 flex-wrap flex-row-reverse">
        <span
          v-for="label in entry.labels"
          :key="label.id"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
        >
          {{ label.label_name }}
        </span>
      </div>
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Standing
      </label>
      {{ entry.standing }}
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Corporation standing
      </label>
      {{ entry.corporation_standing != null ? entry.corporation_standing : 'N.A.' }}
    </div>
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Alliance standing
      </label>
      {{ entry.alliance_standing != null ? entry.alliance_standing : 'N.A.' }}
    </div>
  </li>
</template>

<script>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
export default {
    name: "CharacterContactsRowComponent",
    components: {EntityByIdBlock},
    props: {
        entry: {
            required: true,
            type: Object,
        },
        even: {
            required: true,
            type: Number
        }
    },
    computed: {
        has_standing_offset() {
            if(_.isNil(this.entry.corporation_standing) && _.isNil(this.entry.alliance_standing)) {
                return false
            }

            let standing = this.entry.standing

            if(standing === 0) {
                return false
            }

            let corp_standing = this.entry.corporation_standing != null ? this.entry.corporation_standing : 0
            let alliance_standing = this.entry.alliance_standing != null ? this.entry.alliance_standing : 0

            return !((this.diff(corp_standing,standing) === 0) || (this.diff(alliance_standing,standing) === 0));

        }
    },
    methods: {
        diff(a,b) {
            return a > b ? a - b : b - a
        }
    }
}
</script>

<style scoped>

</style>