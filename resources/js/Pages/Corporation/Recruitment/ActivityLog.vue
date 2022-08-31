<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title> {{ title(pageTitle) }}</title>
    </teleport>
  </div>
  <PageHeader>
    {{ pageTitle }}
  </PageHeader>

  <LogTab :application="application" />
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import LogTab from "@/Pages/Corporation/Recruitment/Tabs/LogTab.vue";

export default {
    name: "ActivityLog",
    components: {LogTab, PageHeader},
    props: {
        application: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            pageTitle: 'Activity Log'
        }
    },
}
</script>

<style scoped />