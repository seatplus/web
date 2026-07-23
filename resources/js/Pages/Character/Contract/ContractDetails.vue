<template>
  <div class="space-y-3">
    <PageHeader
      :page-title="pageTitle"
      :breadcrumbs="breadcrumbs"
    />

    <ContractDetailsComponent
      v-if="contract.items.length > 0"
      :contract="contract"
    />
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ContractDetailsComponent from "@/Shared/Components/Contracts/ContractDetailsComponent.vue";
import { index as characterContracts } from "@/actions/Seatplus/Web/Http/Controllers/Character/ContractsController";

defineProps({
    contract: {
        required: true,
        type: Object
    },
    activeSidebarElement: {
        type: String,
        required: true
    }
});

const pageTitle = 'Contract Details'
const breadcrumbs = [
    {
        name: 'Character Contracts',
        route: characterContracts.url()
    }
]
</script>

<style scoped>

</style>
