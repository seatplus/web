<template>
    <Layout page="Corporation" page-description="Member Compliance">

        <template v-slot:title>
            <PageHeader>
                Corporation Member Compliance
            </PageHeader>
        </template>

        <div class="grid gap-6 grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="col-span-2 space-y-6" >
                <CardWithHeader v-for="corporation of corporations" :key="corporation.corporation_id" >
                    <template v-slot:header>
                        <EntityBlock :entity="corporation" />
<!--                        <div class="ml-4 mt-2 inline-flex items-center space-x-4 px-4 py-5 sm:px-6">
                            <EveImage :object="corporation" />
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ corporation.name }}
                            </h3>
                        </div>-->
                    </template>

                    <CharacterCompliance v-if="corporation.type !== 'user'" :corporation_id="corporation.corporation_id" :query-param="queryParam"/>
                    <UserCompliance v-if="corporation.type === 'user'" :corporation_id="corporation.corporation_id" :query-param="queryParam"/>

                </CardWithHeader>
            </div>

            <div class="col-span-3 md:col-span-2 lg:col-span-1">
                <RadioListWithDescription v-model="selectedModula" :options="filterOptions" title="compliance" class="overflow-hidden shadow rounded-lg"/>
            </div>
        </div>


    </Layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import PageHeader from "@/Shared/Layout/PageHeader";
import HeaderButton from "@/Shared/Layout/HeaderButton";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EveImage from "@/Shared/EveImage";
import CharacterCompliance from "./CharacterCompliance";
import UserCompliance from "./UserCompliance";
import RadioListWithDescription from "@/Shared/Layout/RadioListWithDescription";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";

export default {
    name: "MemberCompliance",
    components: {
        EntityBlock,
        RadioListWithDescription,
        UserCompliance, CharacterCompliance, EveImage, CardWithHeader, HeaderButton, PageHeader, Layout},
    props: {
        corporations: {
            required: true
        }
    },
    data() {
        return {
            selectedModula: 0,
            queryParam: '',
            filterOptions: [
                {title: 'Default', description: 'Show all members'},
                {title: 'Renegades only', description: 'Show only renegades'},
                {title: 'Loyalists only', description: 'Show only Loyalists'}
            ]
        }
    },
    watch: {
        selectedModula(newValue) {

            this.queryParam = this.selectedModula === 1 ? 'renegades' : this.selectedModula === 2 ? 'loyalists' : ''
        }
    }
}
</script>

<style scoped>

</style>
