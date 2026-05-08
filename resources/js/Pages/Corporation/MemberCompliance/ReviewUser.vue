<template>
  <div class="space-y-3">
    <div>
      <PageHeader :breadcrumbs="[{name: 'Compliance ', route: member_compliance().url}]">
        Review: {{ member.main_character.name }}
      </PageHeader>
      <div class="pt-1.5">
        <p class="text-sm font-medium text-gray-500">
          {{ getCharacterNames(member) }}
        </p>
      </div>
    </div>

    <TabComponent
      :recruit="member"
      :watchlist="watchlist"
      :target-corporation="targetCorporation"
    />
  </div>
</template>

<script>
import TabComponent from "../Recruitment/TabComponent.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import { member_compliance } from '@/routes/corporation'
export default {
    name: "ReviewUser",
    components: {PageHeader, TabComponent},
    props: {
        member: {
            required: true,
            type: Object
        },
        targetCorporation: {
            required: true,
            type: Object
        },
        watchlist: {
            required: true,
            type: Object
        },
    },
    setup() {
        return { member_compliance }
    },
    methods: {
        getCharacterNames(member) {

            let characters = _.map(member.characters, character => character.name)

            return _.shuffle(characters).join(', ')
        },
    }
}
</script>
