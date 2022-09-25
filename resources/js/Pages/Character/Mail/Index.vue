<template>
  <MultiColumnLayout>
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>
    <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8">
      <div class="space-y-3">
        <RequiredScopesWarning :dispatch-transfer-object="dispatchTransferObject" />


        <PageHeader class="">
          {{ pageTitle }}
          <template #primary>
            <DispatchUpdateButton />
          </template>
          <template #secondary>
            <EntitySelectionButton />
          </template>
        </PageHeader>

        <div class="block lg:hidden">
          <MobileMailList
            v-model:selectedId="selectedId"
            :character-ids="characterIds"
          />
        </div>

        <div class="hidden md:block space-y-3">
          <MailRepresentation
            v-if="selectedId"
            :key="selectedId"
            :mail-id="selectedId"
          />
        </div>
      </div>
    </div>
    <template #aside>
      <DesktopMailList
        v-model:selectedId="selectedId"
        :character-ids="characterIds"
      />
      <!--      <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
        <MailList
          v-model:selectedId="selectedId"
          :character-ids="characterIds"
        />
      </div>-->
    </template>
  </MultiColumnLayout>
</template>

<script>
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import MultiColumnLayout from "@/Shared/SidebarLayout/MultiColumnLayout.vue";
import {ref} from "vue";
import MailRepresentation from "@/Shared/Components/Mails/MailRepresentation.vue";
import DesktopMailList from "@/Shared/Components/Mails/DesktopMailList.vue";
import MobileMailList from "@/Shared/Components/Mails/MobileMailList.vue";
export default {
    name: "Index",
    components: {
        MobileMailList,
        DesktopMailList,
        MailRepresentation,
        MultiColumnLayout, EntitySelectionButton, DispatchUpdateButton, PageHeader, RequiredScopesWarning},
    layout: null,
    props: {
        dispatchTransferObject: {
            required: true,
            type: Object
        },
        characterIds: {
            required: true,
            type: Array
        }
    },
    setup() {

        const selectedId = ref(null)

        return {
            pageTitle: 'Character Mails',
            selectedId
        }
    }
}
</script>

<style scoped>

</style>

