<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>

  <PageHeader>
    {{ pageTitle }}
  </PageHeader>

  <div class="grid gap-6 grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="col-span-2 space-y-6">
      <CardWithHeader
        v-for="corporation of corporations"
        :key="corporation.corporation_id"
      >
        <template #header>
          <EntityBlock :entity="corporation" />
        </template>

        <CharacterCompliance
          v-if="corporation.type !== 'user'"
          :key="`${corporation.corporation_id}:${selectedModula}`"
          :parameters="{corporation_id: corporation.corporation_id, filter: parameter}"
        />
        <UserCompliance
          v-if="corporation.type === 'user'"
          :key="`${corporation.corporation_id}:${selectedModula}`"
          :parameters="{corporation_id: corporation.corporation_id, filter: parameter}"
        />
      </CardWithHeader>
    </div>

    <div class="col-span-3 md:col-span-2 lg:col-span-1">
      <RadioListWithDescription
        v-model="selectedModula"
        :options="filterOptions"
        title="compliance"
        class="overflow-hidden shadow rounded-lg"
      />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import CharacterCompliance from "./CharacterCompliance";
import UserCompliance from "./UserCompliance";
import RadioListWithDescription from "@/Shared/Layout/RadioListWithDescription";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";

export default {
    name: "MemberCompliance",
    components: {
        EntityBlock,
        RadioListWithDescription,
        UserCompliance, CharacterCompliance, CardWithHeader, PageHeader},
    props: {
        corporations: {
            required: true
        }
    },
    data() {
        return {
          pageTitle: 'Corporation Member Compliance',
            selectedModula: 0,
            queryParam: '',
            filterOptions: [
                {title: 'Default', description: 'Show all members'},
                {title: 'Renegades only', description: 'Show only renegades'},
                {title: 'Loyalists only', description: 'Show only Loyalists'}
            ]
        }
    },
  computed: {
      parameter() {
        return this.queryParam === '' ? null : this.queryParam;
      }
  },
  watch: {
      selectedModula() {

          this.queryParam = this.selectedModula === 1 ? 'renegades' : this.selectedModula === 2 ? 'loyalists' : ''
      }
  },
  methods: {
    getCharacterComplianceUrl(corporation_id) {

      return this.$route('character.compliance', {corporation_id: corporation_id, filter: this.parameter})
    },
    getUserComplianceUrl(corporation_id) {

      return this.$route('user.compliance', {corporation_id: corporation_id, filter: this.parameter})
    }
  }
}
</script>

<style scoped>

</style>
