<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>
    <PageHeader>
      {{ pageTitle }}
      <template #primary>
        <CreateGroupButton />
      </template>
    </PageHeader>
    <ControlGroups
      :roles="result"
    />
    <div ref="scrollComponent"></div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import CreateGroupButton from "./AclTypes/CreateGroupButton";
import ControlGroups from "./ControlGroups";
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

<style scoped>

</style>
