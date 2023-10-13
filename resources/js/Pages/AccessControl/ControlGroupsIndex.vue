<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle">
      <template #primary>
        <CreateGroupButton />
      </template>
    </PageHeader>
    <ControlGroups
      :roles="result"
    />
    <div ref="scrollComponent" />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import CreateGroupButton from "./AclTypes/CreateGroupButton.vue";
import ControlGroups from "./ControlGroups.vue";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
  name: "ControlGroupsIndex",
  components: {
    ControlGroups,
    CreateGroupButton,
    PageHeader,
    //InfiniteLoading
  },
  setup() {

    return useInfinityScrolling('get.acl')
  },
  data() {
    return {
      pageTitle: 'Access Control',
    }
  }
}
</script>
