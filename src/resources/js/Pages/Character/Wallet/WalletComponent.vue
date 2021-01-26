<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <WalletJournalComponent :id="id" />
        <WalletTransactionComponent :id="id" />
    </div>
</template>

<script>
import WalletJournalComponent from "@/Shared/Components/Wallet/Journal/WalletJournalComponent";
import WalletTransactionComponent from "@/Shared/Components/Wallet/Transaction/WalletTransactionComponent";
export default {
    name: "WalletComponent",
    components: {WalletTransactionComponent, WalletJournalComponent},
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
