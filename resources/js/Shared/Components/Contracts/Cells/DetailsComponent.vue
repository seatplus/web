<template>
  <p class="flex items-center text-sm text-gray-500">
    <StatusOnlineIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    {{ contract.status }}
  </p>
  <p
    :class="contract.price > 0 ? 'text-gray-500' :'text-red-500'"
    class="flex items-center text-sm"
  >
    <CashIcon class="shrink-0 mr-1.5 h-5 w-5" />
    Price {{ contract.price.toLocaleString() }}
  </p>
  <p
    v-if="contract.reward > 0"
    class="flex items-center text-sm text-gray-500"
  >
    <CashIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Reward {{ contract.reward.toLocaleString() }}
  </p>
  <p
    v-if="contract.title"
    class="flex items-center text-sm text-gray-500"
  >
    <TagIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    {{ contract.title }}
  </p>
  <p
    v-if="contract.collateral > 0"
    class="flex items-center text-sm text-gray-500"
  >
    <CashIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Collateral {{ contract.collateral.toLocaleString() }}
  </p>
  <p
    v-if="contract.volume > 0"
    class="flex items-center text-sm text-gray-500"
  >
    <ArchiveIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Volume {{ contract.volume.toLocaleString() }}
  </p>
</template>

<script>
import {
    TagIcon,
    StatusOnlineIcon,
    CashIcon,
    ArchiveIcon,
} from "@heroicons/vue/solid";
export default {
    name: "DetailsComponent",
    components: {
        TagIcon,
        StatusOnlineIcon,
        CashIcon,
        ArchiveIcon
    },
    props: {
        contract: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>