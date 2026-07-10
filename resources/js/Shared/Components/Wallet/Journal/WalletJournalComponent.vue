<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Journal
        </div>
      </div>
    </template>
    <!-- scroll-region: lets Inertia track/restore this custom scroll container's
         position, so <InfiniteScroll> merges the next page without jumping to top. -->
    <div
      class="relative max-h-96 overflow-y-auto"
      scroll-region=""
    >
      <div class="hidden sm:grid sm:grid-cols-12 sm:gap-x-0 sm:gap-y-0.5 grid-flow-row z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500">
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Date
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          Type
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-3">
          Amount
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-3">
          Balance
        </div>
        <div class="px-6 sm:px-3 py-1 col-span-2">
          <span class="sr-only">Expand</span>
        </div>
      </div>

      <!-- Character + corporation wallets both use native Inertia infinite scroll
           over a page scroll prop (keyed per character, or per corporation+division). -->
      <InfiniteScroll
        :data="scrollKey"
        :items-element="`#${scrollBodyId}`"
        preserve-url
      >
        <ul
          :id="scrollBodyId"
          class="relative z-0 divide-y divide-gray-200"
        >
          <WalletJournalRowComponent
            v-for="(entry, index) in journalEntries"
            :key="entry.id"
            :entry="entry"
            :even="index%2"
          />
        </ul>
      </InfiniteScroll>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import WalletJournalRowComponent from "./WalletJournalRowComponent.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import { InfiniteScroll } from "@inertiajs/vue3";

export default {
    name: "WalletJournalComponent",
    components: {
        InfiniteScroll,
        EntityByIdBlock,
        WalletJournalRowComponent,
        CardWithHeader
    },
    props: {
        id: {
            required: true,
            type: Number
        },
        division: {
            required: false,
            type: Object,
            default: () => {}
        },
        filters: {
            required: false,
            type: Object,
            default: () => new Object()
        }
    },
    computed: {
        // The journal is delivered as a page scroll prop (WalletsController /
        // CorporationWalletController::index): keyed per character, or per
        // corporation+division for corporate wallets, and consumed by
        // <InfiniteScroll :data="scrollKey">.
        scrollKey() {
            return this.division
                ? `journal_${this.id}_${this.division.division_id}`
                : `journal_${this.id}`
        },
        scrollBodyId() {
            return this.division
                ? `journal-body-${this.id}-${this.division.division_id}`
                : `journal-body-${this.id}`
        },
        journalEntries() {
            return this.$page.props[this.scrollKey]?.data ?? []
        }
    }
}
</script>

<style scoped>

</style>
