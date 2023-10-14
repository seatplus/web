<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" :breadcrumbs="breadcrumbs" />

    <ContractDetailsComponent
      v-if="contract.items.length > 0"
      :contract="contract"
    />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ContractDetailsComponent from "@/Shared/Components/Contracts/ContractDetailsComponent.vue";
export default {
    name: "ContractDetails",
    components: {
        ContractDetailsComponent,
        PageHeader
    },
    props: {
        contract: {
            required: true,
            type: Object
        },
        activeSidebarElement: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            pageTitle: 'Contract Details',
            breadcrumbs: [
                {
                    name: 'Character Contracts',
                    route: route('character.contracts')
                }
            ]
        }
    },
}
</script>

<style scoped>

</style>
