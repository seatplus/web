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
          <MailList
            v-model:selectedId="selectedId"
            :character-ids="characterIds"
          />
        </div>

        <MailRepresentation
          v-if="selectedId"
          :key="selectedId"
          :mail-id="selectedId"
        />
      </div>
    </div>
    <template #aside>
      <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
        <MailList
          v-model:selectedId="selectedId"
          :character-ids="characterIds"
        />
      </div>
    </template>
  </MultiColumnLayout>
</template>

<script>
import RequiredScopesWarning from "../../../Shared/SidebarLayout/RequiredScopesWarning";
import PageHeader from "../../../Shared/Layout/PageHeader";
import DispatchUpdateButton from "../../../Shared/Components/SlideOver/DispatchUpdateButton";
import EntitySelectionButton from "../../../Shared/Components/SlideOver/EntitySelectionButton";
import MultiColumnLayout from "../../../Shared/SidebarLayout/MultiColumnLayout";
import MailList from "../../../Shared/Components/Mails/MailList";
import {ref} from "vue";
import MailRepresentation from "../../../Shared/Components/Mails/MailRepresentation";
export default {
    name: "Index",
    components: {
        MailRepresentation,
        MailList,
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

