<template>
    <div class="flex-grow overflow-y-auto overflow-x-auto hidden sm:block">
        <table class="relative table-fixed w-full">
            <thead class="sticky top-0 bg-gray-50">
            <TableHeader>
                <DataHeader class="w-2/6 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contact
                </DataHeader>
                <DataHeader class="w-1/6 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Labels
                </DataHeader>
                <DataHeader class="w-1/6 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Standing
                </DataHeader>
                <DataHeader class="w-1/6 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Corporation standing
                </DataHeader>
                <DataHeader class="w-1/6 sticky top-0 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Alliance standing
                </DataHeader>
            </TableHeader>
            </thead>
            <tbody>
            <TableRow v-for="(contact, index) in contacts" :key="contact.contact_id" :class="index%2 ? 'bg-gray-50' : 'bg-white'">
                <DataCell class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                    <EntityBlock :entity="contact.entity"/>
                </DataCell>
                <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-for="label in contact.labels" type="button" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        {{ label }}
                    </div>
                </DataCell>
                <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ contact.standing }}
                </DataCell>
                <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ contact.corporation_standing }}
                </DataCell>
                <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ contact.alliance_standing }}
                </DataCell>
            </TableRow>
            </tbody>
        </table>
    </div>
</template>

<script>
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
import TableRow from "@/Shared/Layout/Cards/Table/TableRow";
import DataCell from "@/Shared/Layout/Cards/Table/DataCell";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
export default {
    name: "ContactsTable",
    components: {EntityBlock, DataCell, TableRow, DataHeader, TableHeader},
    props: {
        contacts: {
            type: Array,
            required: true
        }
    }
}
</script>

<style scoped>

</style>
