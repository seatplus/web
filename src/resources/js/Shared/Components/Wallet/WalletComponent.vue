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
      :division="division"
    />
    <WalletTransactionComponent
      :id="id"
      :division="division"
    />
  </div>
</template>

<script>
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent";
import WalletJournalBalanceChart from "./Journal/WalletJournalBalanceChart";
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
        }
    }
}
</script>

<style scoped>

</style>
