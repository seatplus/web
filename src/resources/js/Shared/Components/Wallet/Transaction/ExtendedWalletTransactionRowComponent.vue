<template>
  <div class="space-y-4 divide-y divide-gray-200 sm:space-y-3">
    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
      <div class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
        Unit Price
      </div>
      <div class="mt-1 sm:mt-0 sm:col-span-2">
        <p class="mt-2 text-sm whitespace-normal text-gray-500">
          {{ entry.unit_price.toLocaleString() }}
        </p>
      </div>
    </div>

    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
      <div class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
        Quantity
      </div>
      <div class="mt-1 sm:mt-0 sm:col-span-2">
        <p class="mt-2 text-sm whitespace-normal text-gray-500">
          {{ entry.quantity.toLocaleString() }}
        </p>
      </div>
    </div>

    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
      <div class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
        Is personal
      </div>
      <div class="mt-1 sm:mt-0 sm:col-span-2">
        <p class="mt-2 text-sm whitespace-normal text-gray-500">
          {{ entry.is_personal ? 'true' : 'false' }}
        </p>
      </div>
    </div>

    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
      <div class="block text-sm font-medium text-gray-700">
        Location
      </div>
      <div class="mt-1 sm:mt-0 sm:col-span-2">
        <div
          v-if="entry.location"
          class="flex items-center"
        >
          <div class="flex-shrink-0">
            <EveImage
              :object="entry.location.locatable"
              :size="256"
              tailwind_class="h-12 w-12 rounded-full"
            />
          </div>
          <div class="ml-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 whitespace-normal">
              {{ entry.location.locatable.name }}
            </h3>
          </div>
        </div>
      </div>
    </div>


    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:py-5">
      <div class="block text-sm font-medium text-gray-700">
        Client
      </div>
      <div class="mt-1 sm:mt-0 sm:col-span-2">
        <EntityByIdBlock :id="entry.client_id" />
      </div>
    </div>
  </div>
</template>

<script>
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import EveImage from "@/Shared/EveImage";
export default {
    name: "ExtendedWalletTransactionRowComponent",
    components: {EveImage, EntityByIdBlock},
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
