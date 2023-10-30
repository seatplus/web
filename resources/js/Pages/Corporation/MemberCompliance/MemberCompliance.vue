<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle">
      <template #primary>
        <ComplianceTabs
          v-if="corporations.length > 0"
          v-model="queryParam"
        />
      </template>
    </PageHeader>

    <div
      v-if="corporations.length === 0"
      class="text-center"
    >
      <ArchiveBoxXMarkIcon class="mx-auto h-12 w-12 text-gray-400" />
      
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        No corporation or alliance sso scopes found.
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        Ask your admin, to configure corporation or alliance sso scopes to be used for compliance.
      </p>
    </div>

    <ComplianceComponent
      v-for="corporation of corporations"
      :key="corporation.corporation_id"
      :corporation="corporation"
      :query-param="queryParam"
      :can-review="canReview"
    />
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ComplianceTabs from "./ComplianceTabs.vue";
import ComplianceComponent from "./ComplianceComponent.vue";
import {ref, watch} from "vue";
import {ArchiveBoxXMarkIcon} from "@heroicons/vue/24/outline";

defineProps({
  corporations: {
    required: true,
    type: Array,
  },
  canReview: {
    required: true,
    type: Boolean,
  },
});

const pageTitle = 'Corporation Member Compliance';
const selectedModula = ref(0);
const queryParam = ref('default');

watch(selectedModula, () => {
  queryParam.value = selectedModula.value === 1 ? 'renegades' : selectedModula.value === 2 ? 'loyalists' : ''
})


</script>

<style scoped>

</style>
