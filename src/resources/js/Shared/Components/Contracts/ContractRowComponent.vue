<template>
  <li>
    <inertia-link
      v-if="contract.items > 0"
      :href="url"
    >
      <ContractRowElements
        :entity="entity"
        :contract="contract"
      />
    </inertia-link>
    <ContractRowElements
      v-else
      :entity="entity"
      :contract="contract"
    />
  </li>

</template>

<script>
import ContractRowElements from "./ContractRowElements";
export default {
    name: "ContractRowComponent",
    components: {ContractRowElements, },
    props: {
        contract: {
            type: Object,
            required: true
        },
        entity: {
            type: Object,
            required: false
        }
    },
    computed: {
        url() {
            return this.$route('contract.details', {
                character_id: this.entity.character_id,
                contract_id: this.contract.contract_id
            });
        }
    }
}
</script>

<style scoped>

</style>
