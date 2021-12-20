<template>
  <EntityByIdBlock
    :id="contract.assignee_id"
    :image-size="10"
    name-font-size="sm"
  />

  <EntityByIdBlock
    v-if="contract.acceptor_id !== 0 && contract.acceptor_id !== contract.assignee_id"
    :id="contract.acceptor_id"
    class="ml-6"
    :image-size="6"
    name-font-size="xs"
  />
</template>

<script>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
export default {
    name: "AssigneeComponent",
    components: {EntityByIdBlock},
    props: {
        contract: {
            required: true,
            type: Object
        }
    }
}
</script>