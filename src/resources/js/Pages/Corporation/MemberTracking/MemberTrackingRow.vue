<template>
    <TableRow>
        <DataCell class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-900">
            <div class="flex-shrink-0">

                <svg v-if="isOk" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
        </DataCell>
        <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
            <EntityBlock :entity="member.character" v-if="member.character"/>
            <EntityByIdBlock :id="member.character_id" :withSubText="false" v-else />
        </DataCell>
        <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
            {{ locationName }}
        </DataCell>
        <DataCell class="px-6 py-4 hidden md:block whitespace-normal text-sm text-gray-500">
            <div class="inline-flex items-center space-x-4">
                <EveImage :object="{type_id: member.ship_type_id}" :size="256" tailwind_class="h-6 w-6 rounded-full" />
                <span v-if="member.ship">{{ member.ship.name }}</span>
            </div>
        </DataCell>
        <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
            <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.start_date" />
        </DataCell>
        <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
            <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.logon_date" />
        </DataCell>
    </TableRow>
</template>

<script>
import TableRow from "@/Shared/Layout/Cards/Table/TableRow";
import DataCell from "@/Shared/Layout/Cards/Table/DataCell";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import EveImage from "@/Shared/EveImage";
import Time from "@/Shared/Time";
export default {
    name: "MemberTrackingRow",
    components: {Time, EveImage, EntityByIdBlock, EntityBlock, DataCell, TableRow},
    props: {
        member: {
            required: true,
            type: Object
        },
        required_scopes: {
            required: true,
            type: Array
        }
    },
    methods: {
        has(string) {
            return _.has(this.member, string)
        }
    },
    computed: {
        missing_scopes() {

            return _.differenceWith(this.required_scopes, this.refresh_token_scopes, _.isEqual)
        },
        refresh_token_scopes() {
            return _.get(this.member, 'character.refresh_token.scopes', [])
        },
        isOk() {
            return _.isEmpty(this.missing_scopes) && !_.isEmpty(this.refresh_token_scopes)
        },
        locationName() {
            return _.get(this.member, 'location.name', 'Unknown Location')
        }
    }
}
</script>

<style scoped>

</style>
