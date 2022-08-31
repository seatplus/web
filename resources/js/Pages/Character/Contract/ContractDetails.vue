<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>
    
    <PageHeader :breadcrumbs="breadcrumbs">
      Contract Details
    </PageHeader>

    <ContractDetailsComponent
      v-if="contract.items.length > 0"
      :contract="contract"
    />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import ContractDetailsComponent from "@/Shared/Components/Contracts/ContractDetailsComponent";
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
                    route: this.route('character.contracts')
                }
            ]
        }
    },
}
</script>

<style scoped>

</style>
