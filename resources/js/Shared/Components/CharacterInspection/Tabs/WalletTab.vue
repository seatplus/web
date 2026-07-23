<template>
  <WalletFilter :model-value="filter" />
  <div
    v-for="character_id in characterIds"
    :key="`wallet component ${character_id}`"
    class="space-y-4"
  >
    <WalletJournalBalanceChart :id="character_id" />
    <WalletJournalComponent
      :id="character_id"
      :key="ref_types"
      :filters="{ ref_type: ref_types }"
    />
    <WalletTransactionComponent :id="character_id" />
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import WalletJournalBalanceChart from "@/Shared/Components/Wallet/Journal/WalletJournalBalanceChart.vue";
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent.vue";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent.vue";
import WalletFilter from "@/Shared/Components/Wallet/WalletFilter.vue";

defineProps({
    characterIds: {
        required: true,
        type: Array
    }
});

const filter = ref([])

const ref_types = computed(() => _.map(filter.value, (ref_type) => ref_type.name))
</script>

<style scoped>

</style>