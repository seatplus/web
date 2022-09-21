<template>
  <a
    v-if="contract.items > 0"
    :href="url"
    @click.stop.prevent="open()"
  >
    <ArrowsExpandIcon
      class="shrink-0 h-5 w-5 text-gray-500 cursor-pointer hover:text-gray-400"
    />
  </a>
  <teleport to="#destination">
    <WithDismissButtonModal
      v-model="openModal"
      width="5xl"
    >
      <DialogTitle
        as="h3"
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Contract Details
      </DialogTitle>
      <ContractDetailsComponent
        v-if="contractDetails"
        :contract="contractDetails"
        class="mt-4"
      />
    </WithDismissButtonModal>
  </teleport>
</template>
<script>
import {ArrowsExpandIcon} from "@heroicons/vue/20/solid";
import {ref} from "vue";
import WithDismissButtonModal from "@/Shared/Modals/WithDismissButtonModal.vue";
import {DialogTitle} from "@headlessui/vue";
import ContractDetailsComponent from "./ContractDetailsComponent.vue";

export default {
    name: "ExpandContractComponent",
    components: {
        ContractDetailsComponent,
        WithDismissButtonModal, ArrowsExpandIcon, DialogTitle},
    props: {
        contract: {
            required: true,
            type: Object
        },
        characterId: {
            required: true,
            type: Number
        }
    },
    setup(props) {
        const openModal = ref(false)
        const contractDetails = ref()

        const url = route('contract.details', {character_id: props.characterId, contract_id: props.contract.contract_id})

        const fetchDetails = async () => {
            await axios.get(url, {headers: {'X-Modal': true}}).then(response => contractDetails.value = response.data[0])
        }

        const open = () => {
            if(!contractDetails.value) {
                fetchDetails()
            }
            openModal.value = true
        }

        return {
            openModal,
            open,
            contractDetails,
            url
        }
    }
}
</script>

<style scoped>

</style>