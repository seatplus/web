<template>
  <MultiColumnLayout>
    <div class="absolute inset-0 py-6 px-4 sm:px-6 lg:px-8">
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

        <div class="block lg:hidden">
          <MobileMailList v-model:selected-id="selectedId" />
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
      <DesktopMailList v-model:selected-id="selectedId" />
    </template>
  </MultiColumnLayout>
</template>

<script>
export default {
    layout: null,
}
</script>

<script setup>
import {ref} from "vue";
import RequiredScopesWarning from "@/Shared/SidebarLayout/RequiredScopesWarning.vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import DispatchUpdateButton from "@/Shared/Components/SlideOver/DispatchUpdateButton.vue";
import EntitySelectionButton from "@/Shared/Components/SlideOver/EntitySelectionButton.vue";
import MultiColumnLayout from "@/Shared/SidebarLayout/MultiColumnLayout.vue";
import MailRepresentation from "@/Shared/Components/Mails/MailRepresentation.vue";
import DesktopMailList from "@/Shared/Components/Mails/DesktopMailList.vue";
import MobileMailList from "@/Shared/Components/Mails/MobileMailList.vue";

defineProps({
    dispatchTransferObject: {
        required: true,
        type: Object
    },
    characterIds: {
        required: true,
        type: Array
    }
});

const selectedId = ref(null)

const pageTitle = 'Character Mails'
</script>

<style scoped>

</style>

