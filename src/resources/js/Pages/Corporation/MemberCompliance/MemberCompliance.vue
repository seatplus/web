<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <ComplianceTabs v-model="queryParam" />
      </template>
    </PageHeader>

    <ComplianceComponent
      v-for="corporation of corporations"
      :key="corporation.corporation_id"
      :corporation="corporation"
      :query-param="queryParam"
    />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import ComplianceTabs from "./ComplianceTabs";
import ComplianceComponent from "./ComplianceComponent";

export default {
    name: "MemberCompliance",
    components: {
        ComplianceComponent,
        ComplianceTabs,
        PageHeader},
    props: {
        corporations: {
            required: true
        }
    },
    data() {
        return {
          pageTitle: 'Corporation Member Compliance',
            selectedModula: 0,
            queryParam: 'default'
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
