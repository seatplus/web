<template>
  <div class="space-y-3">
    <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />

    <PageHeader :page-title="pageTitle">
      <template #primary>
        <DispatchUpdateButton />
      </template>
      <template #secondary>
        <EntitySelectionButton />
      </template>
    </PageHeader>


    <div class="space-y-4">
      <WalletFilter
        v-model="filter"
        :ref-types="ref_types"
      />
      <WalletComponent
        v-for="character_id of character_ids"
        :id="character_id"
        :key="character_id"
        :filters="{ref_type: filter}"
      />
    </div>
  </div>
</template>

<script setup>
import {computed, ref, watch} from "vue";
import { router } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import WalletComponent from "@/Shared/Components/Wallet/WalletComponent.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import WalletFilter from "@/Shared/Components/Wallet/WalletFilter.vue";

const props = defineProps({
    dispatchTransferObject: {
        required: true,
        type: Object
    },
    character_ids: {
        required: true,
        type: Array
    },
    // Available ref_type options for the filter (WalletsController::index) —
    // passed as a prop so the filter needs no autosuggest endpoint (no axios/Ziggy).
    ref_types: {
        required: false,
        type: Array,
        default: () => []
    }
});

const pageTitle = 'Character Wallets'
const filter = ref([]) // selected ref_type strings

// Scroll-prop keys for every character card (WalletsController::index).
const journalKeys = computed(() => props.character_ids.map((id) => `journal_${id}`))

// The ref_type filter is a page-level query param: reload only the journal
// scroll props so <InfiniteScroll> resets to the filtered first page.
watch(filter, (newValue) => {
    router.reload({
        only: journalKeys.value,
        // reset: without it InfiniteScroll *merges* the filtered page onto the
        // existing rows instead of replacing them, so the filter looks ignored.
        reset: journalKeys.value,
        data: { ref_type: newValue },
        preserveState: true,
        preserveScroll: true,
    })
})
</script>

<style scoped>

</style>
