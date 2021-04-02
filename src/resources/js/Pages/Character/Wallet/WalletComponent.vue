<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <WalletJournalBalanceChart :id="id" class="col-span-2"/>
    <WalletJournalComponent :id="id" />
    <WalletTransactionComponent :id="id" />
  </div>
</template>

<script>
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent";
import WalletJournalBalanceChart from "../../../Shared/Components/Wallet/Journal/WalletJournalBalanceChart";
export default {
    name: "WalletComponent",
    components: {WalletJournalBalanceChart, WalletTransactionComponent, WalletJournalComponent},
    props: {
        id: {
            required: true,
            type: Number
        },
        divisions: {
            required: false,
            type: Array
        }
    }
}
</script>

<style scoped>

</style>
