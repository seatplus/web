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

<script>
import WalletJournalBalanceChart from "../../../../Shared/Components/Wallet/Journal/WalletJournalBalanceChart";
import WalletJournalComponent from "../../../../Shared/Components/Wallet/Journal/WalletJournalComponent";
import WalletTransactionComponent from "../../../../Shared/Components/Wallet/Transaction/WalletTransactionComponent";
import WalletFilter from "../../../../Shared/Components/Wallet/WalletFilter";
export default {
    name: "WalletTab",
    components: {WalletFilter, WalletTransactionComponent, WalletJournalComponent, WalletJournalBalanceChart},
    props: {
        characterIds: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            filter: []
        }
    },
    computed: {
        ref_types() {
            return _.map(this.filter, (ref_type) => ref_type.name)
        }
    }
}
</script>

<style scoped>

</style>