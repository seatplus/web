<template>
  <div>
    <InfiniteLoadingHelper
      route="get.mail.headers"
      :params="{character_ids: characterIds}"
      @result="assignResult"      
    >
      <ul class="divide-y divide-gray-200">
        <Disclosure
          v-for="mail in mails"
          :key="mail.id"
          v-slot="{open}"
          as="li"
          :class="['block', {'xl:hidden' : !recruitmentView}]"
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
        <template v-if="!recruitmentView">
          <li
            v-for="mail in mails"
            :key="mail.id"
            class="py-4 hidden xl:block"
            @click="emitSelection(mail.id)"
          >
            <div class="flex space-x-3">
              <EveImage
                :object="{character_id: mail.from}"
                tailwind_class="h-6 w-6 rounded-full"
              />
              <div class="flex-1 space-y-1">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-medium">
                    <ResolveIdToName
                      :id="mail.from"
                      :tailwind-class="isSelected(mail) ? 'text-sm font-medium text-indigo-900' : 'text-sm font-medium'"
                    />
                  </h3>
                  <Time
                    :timestamp="mail.timestamp"
                    :class="['text-sm', isSelected(mail) ? 'text-indigo-500' : 'text-gray-500']"
                  />
                </div>
                <p :class="['text-sm', isSelected(mail) ? 'text-indigo-500' : 'text-gray-500']">
                  {{ mail.subject }}
                </p>
              </div>
            </div>
          </li>
        </template>
      </ul>
    </InfiniteLoadingHelper>
  </div>
</template>

<script>
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import {ref} from "vue";
import ResolveIdToName from "../../ResolveIdToName";
import Time from "../../Time";
import EveImage from "../../EveImage";
import {ChevronUpIcon} from "@heroicons/vue/solid";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import MailRepresentation from "./MailRepresentation";
export default {
    name: "MailList",
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
            required: false
        },
        recruitmentView: {
            required: false,
            type: Boolean,
            default: () => false
        }
    },
    emits: ['update:selectedId'],
    setup(props, {emit}) {
        const mails = ref([])

        const assignResult = (results) => mails.value = results
        const emitSelection = (selectedId) => emit('update:selectedId', selectedId)
        const isSelected = (mail) => mail.id === props.selectedId

        return {
            assignResult,
            mails,
            emitSelection,
            isSelected
        }
    }
}
</script>

<style scoped>

</style>