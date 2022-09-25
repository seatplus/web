<template>
  <p class="flex items-center text-sm text-gray-500">
    <SignalIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    {{ contract.status }}
  </p>
  <p
    :class="contract.price > 0 ? 'text-gray-500' :'text-red-500'"
    class="flex items-center text-sm"
  >
    <BanknotesIcon class="shrink-0 mr-1.5 h-5 w-5" />
    Price {{ contract.price.toLocaleString() }}
  </p>
  <p
    v-if="contract.reward > 0"
    class="flex items-center text-sm text-gray-500"
  >
    <BanknotesIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
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
    <BanknotesIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Collateral {{ contract.collateral.toLocaleString() }}
  </p>
  <p
    v-if="contract.volume > 0"
    class="flex items-center text-sm text-gray-500"
  >
    <ArchiveBoxIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
    Volume {{ contract.volume.toLocaleString() }}
  </p>
</template>

<script>
import {
    TagIcon,
    SignalIcon,
    BanknotesIcon,
    ArchiveBoxIcon,
} from "@heroicons/vue/20/solid";
export default {
    name: "DetailsComponent",
    components: {
        TagIcon,
        SignalIcon,
        BanknotesIcon,
        ArchiveBoxIcon
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