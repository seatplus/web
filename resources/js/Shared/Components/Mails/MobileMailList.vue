<template>
  <InfiniteLoadingHelper
    v-slot="{results}"
    route="get.mail.headers"
    :params="{character_ids: characterIds}"
    @result="assignResult"
  >
    <ul class="divide-y divide-gray-200">
      <Disclosure
        v-for="mail in results"
        :key="mail.id"
        v-slot="{open}"
        as="li"
      >
        <DisclosureButton class="flex w-full py-2 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
          <div class="flex w-full space-x-3">
            <EveImage
              :object="{character_id: mail.from}"
              tailwind_class="h-6 w-6 rounded-full"
            />
            <div class="flex-1 space-y-1">
              <div class="flex items-center justify-between">
                <div class="space-x-1 inline-flex">
                  <ResolveIdToName
                    :id="mail.from"
                    :tailwind-class="isSelected(mail) ? 'text-sm font-medium text-indigo-900' : 'text-sm font-medium'"
                  />
                  <Time
                    :timestamp="mail.timestamp"
                    :class="['text-sm', isSelected(mail) ? 'text-indigo-500' : 'text-gray-500']"
                  />
                </div>
                <ChevronUpIcon
                  :class="open ? 'transform rotate-180' : ''"
                  class="w-5 h-5"
                />
              </div>
              <p class="flex text-sm text-gray-500">
                {{ mail.subject }}
              </p>
            </div>
          </div>
        </DisclosureButton>
        <DisclosurePanel class="space-y-2">
          <MailRepresentation
            v-if="open"
            :key="mail.id"
            :mail-id="mail.id"
          />
        </DisclosurePanel>
      </Disclosure>
    </ul>
  </InfiniteLoadingHelper>
</template>

<script>
import MailRepresentation from "./MailRepresentation";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time";
import ResolveIdToName from "../../ResolveIdToName";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import {ChevronUpIcon} from "@heroicons/vue/solid/esm";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

export default {
    name: "MobileMailList",
    components: {
        MailRepresentation,
        EveImage, Time, ResolveIdToName, InfiniteLoadingHelper,
        ChevronUpIcon,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
    },
    props: {
        characterIds: {
            type: Array,
            required: true
        },
        selectedId: {
            type: Number,
            required: false
        }
    },
    emits: ['update:selectedId'],
    setup(props, {emit}) {


        const emitSelection = (selectedId) => emit('update:selectedId', selectedId)
        const isSelected = (mail) => mail.id === props.selectedId

        return {
            emitSelection,
            isSelected
        }
    }
}
</script>

<style scoped>

</style>