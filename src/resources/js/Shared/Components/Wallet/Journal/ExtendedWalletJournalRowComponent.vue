<template>
    <div class="space-y-8 divide-y divide-gray-200">
        <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
            <div>

                <div class="space-y-6 sm:space-y-5">

                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <div class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Description
                        </div>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <p class="mt-2 text-sm whitespace-normal text-gray-500">
                                {{ entry.description }}
                            </p>
                        </div>
                    </div>

                    <div v-if="entry.reason" class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <div class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Reason
                        </div>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <p class="mt-2 text-sm whitespace-normal text-gray-500">
                                {{ entry.reason }}
                            </p>
                        </div>
                    </div>

                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
                        <div class="block text-sm font-medium text-gray-700">
                            Sender
                        </div>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <EntityByIdBlock :id="entry.first_party_id" />
                        </div>
                    </div>
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
                        <div class="block text-sm font-medium text-gray-700">
                            Receiver
                        </div>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <EntityByIdBlock :id="entry.second_party_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";

export default {
    name: "ExtendedWalletJournalRowComponent",
    components: {EntityByIdBlock},
    props: {
        entry: {
            required: true
        }
    },
    data() {
        return {
            expanded: false,
        }
    }
}
</script>

<style scoped>

</style>
