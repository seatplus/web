<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityBlock
          class="flex-grow"
          :entity="corporation"
        />
      </div>
    </template>
    <!--List for small devices-->
    <div class="bg-white shadow overflow-y-auto sm:hidden sm:rounded-md">
      <ul class="divide-y divide-cool-gray-200 max-h-96">
        <MemberTrackingListElement
          v-for="member in result"
          :key="member.character_id"
          :member="member"
          :required_scopes="required_scopes"
        />
        <div ref="scrollComponent" />
      </ul>
    </div>
    <!--Table for medium and above-->
    <div class="sm:flex flex-col hidden max-h-96">
      <div class="flex-grow overflow-y-auto overflow-x-hidden">
        <table class="relative table-fixed w-full">
          <thead class="sticky top-0 bg-gray-50">
            <TableHeader>
              <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase overflow-hidden tracking-wider">
                Token
              </DataHeader>
              <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </DataHeader>
              <DataHeader class="w-3/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Last Location
              </DataHeader>
              <DataHeader class="w-3/12 sticky hidden md:block top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ship
              </DataHeader>
              <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Joined
              </DataHeader>
              <DataHeader class="w-1/12 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Last Login
              </DataHeader>
            </TableHeader>
          </thead>
          <tbody>
            <MemberTrackingRow
              v-for="(member, index) in result"
              :key="member.character_id"
              :member="member"
              :required_scopes="required_scopes"
              :class="index%2 ? 'bg-gray-50' : 'bg-white'"
            />
          </tbody>
        </table>
      </div>
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
import MemberTrackingRow from "./MemberTrackingRow";
import MemberTrackingListElement from "./MemberTrackingListElement";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";

export default {
  name: "MemberTrackingComponent",
  components: {
    MemberTrackingListElement,
    MemberTrackingRow, DataHeader, TableHeader, EntityBlock, CardWithHeader
  },
  props: {
    corporation: {
      required: true,
      type: Object
    }
  },
  setup(props) {

    return useInfinityScrolling('get.corporation.member_tracking', props.corporation.corporation_id)
  },
  data() {
    return {
      infiniteId: +new Date(),
      members: [],
      page: 1,
      required_scopes: []
    }
  }
}
</script>

<style scoped>

</style>
