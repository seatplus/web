<template>
  <div
    v-if="division"
    class="pb-5 border-b border-gray-200"
  >
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      {{ division.name }}
    </h3>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <WalletJournalBalanceChart
      :id="id"
      :division="division"
      class="col-span-2"
    />
    <WalletJournalComponent
      :id="id"
      :key="filters"
      :division="division"
      :filters="filters"
    />
    <WalletTransactionComponent
      :id="id"
      :division="division"
    />
  </div>
</template>

<script>
import WalletJournalComponent from "./Journal/WalletJournalComponent.vue";
import WalletTransactionComponent from "./Transaction/WalletTransactionComponent.vue";
import WalletJournalBalanceChart from "./Journal/WalletJournalBalanceChart.vue";
export default {
    name: "WalletComponent",
    components: {WalletJournalBalanceChart, WalletTransactionComponent, WalletJournalComponent},
    props: {
        id: {
            required: true,
            type: Number
        },
        division: {
            required: false,
            type: Object
        },
        filters: {
            required: false,
            type: Object,
            default: () => new Object()
        }
    }
}
</script>

<style scoped>

</style>
