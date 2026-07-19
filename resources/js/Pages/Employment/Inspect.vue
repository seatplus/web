<template>
  <div class="space-y-4">
    <PageHeader :page-title="`Inspect: ${recruit.main_character?.name ?? 'Member'}`" />

    <p class="text-sm text-gray-500">
      Switch to <strong>Assets</strong>, <strong>Wallets</strong> or <strong>Contracts</strong> to
      review this member against the corporation watchlist.
    </p>

    <TabComponent
      :recruit="recruit"
      :watchlist="watchlist"
      :target-corporation="targetCorporation"
    />
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import TabComponent from "@/Pages/Corporation/Recruitment/TabComponent.vue";

defineProps({
  recruit: {
    required: true,
    type: Object,
  },
  watchlist: {
    required: true,
    type: Object,
  },
  targetCorporation: {
    required: true,
    type: Object,
  },
});
</script>
