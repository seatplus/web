<template>
    <Layout page="Character Contacts" :required-scopes="dispatch_transfer_object.required_scopes">

        <div class="space-y-4">
            <CharacterContactPanel v-for="character_affiliation in characters" :key="character_affiliation.character_id" :character="character_affiliation.character" :corporation_id="character_affiliation.corporation_id" :alliance_id="character_affiliation.alliance_id" />
        </div>


<!--        <button @click="openSlideOver" type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Button text
        </button>-->

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
                <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
            </SlideOver>
        </template>
    </Layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import SlideOver from "@/Shared/Layout/SlideOver";
import DispatchUpdate from "@/Shared/DispatchUpdate";
import CharacterContactPanel from "./CharacterContactPanel";
import ListTransition from "@/Shared/Transitions/ListTransition";
export default {
    name: "Index",
    components: {ListTransition, CharacterContactPanel, DispatchUpdate, SlideOver, Layout},
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        },
        characters: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            entities: [],
            ready: false
        }
    },
    methods: {
        openSlideOver() {
            this.$eventBus.$emit('open-slideOver');
        },
        async getUrl(url, contactables = []) {
            return axios.get(url)
                .then(({ data }) => {

                    let contactable = _.filter([...data.data], (data) => !_.isEmpty(data))

                    contactables.push(...contactable)

                    if(data.links.next)
                        return this.getUrl(data.links.next, contactables)

                    // If no more next pages are present, create contactable
                    let contactable_id = data.meta.entity_id

                    let contactable_object = {'contactable_id': contactable_id, data: contactables}

                    this.entities.push(contactable_object)
                })
                .catch(error => console.log(error))
        },
        getDetail(id) {
            return this.getUrl(this.$route('character.contacts.detail', id))
        },
        getContactsFor(id) {

            let contacts = []

            if(_.isNull(id))
                return []

            contacts = _.find(this.entities, {'contactable_id': id});

            return _.isUndefined(contacts) ? [] : contacts.data;
        }
    },
    created: function () {


    }
}
</script>

<style scoped>

</style>
