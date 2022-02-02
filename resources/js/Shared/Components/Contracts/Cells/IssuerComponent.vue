<template>
  <EntityByIdBlock
    :id="contract.for_corporation === 1 ? contract.issuer_corporation_id : contract.issuer_id"
    :key="contract.for_corporation === 1 ? contract.issuer_corporation_id : contract.issuer_id"
    :image-size="10"
    name-font-size="sm"
  />

  <EntityByIdBlock
    v-if="contract.for_corporation"
    :id="contract.issuer_id"
    :key="contract.issuer_id"
    :with-sub-text="false"
    class="ml-6"
    :image-size="6"
    name-font-size="xs"
  />
</template>

<script>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";

export default {
    name: "IssuerComponent",
    components: {EntityByIdBlock},
    props: {
        contract: {
            required: true,
            type: Object
        }
    }
}
</script>
