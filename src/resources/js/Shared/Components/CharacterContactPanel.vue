<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <transition
        enter-active-class="transition ease-out duration-100"
        enter-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-class="transform opacity-100 scale-100"
        xleave-to-class="transform opacity-0 scale-95"
    >
        <div v-if="ready" class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6">
            <!-- Content goes here -->
            <EntityBlock :entity="character"/>

        </div>
        <div class="">
            <!-- This example requires Tailwind CSS v2.0+ -->
            <SimpleStriped>
                <template v-slot:header>
                    <TableHeader>
                        <DataHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </DataHeader>
                        <DataHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Labels
                        </DataHeader>
                        <DataHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Standing
                        </DataHeader>
                        <DataHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Corporation standing
                        </DataHeader>
                        <DataHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Alliance standing
                        </DataHeader>
                    </TableHeader>
                </template>
                <TableRow v-for="(entity, index) in enriched_entities" :key="entity.contact_id" :class="index%2 ? 'bg-gray-50' : 'bg-white'">
                    <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <EntityBlock :entity="entity"/>
                    </DataCell>
                    <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div v-for="label in getLabels(entity)" type="button" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600">
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            {{ label }}
                        </div>
                    </DataCell>
                    <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ entity.standing }}
                    </DataCell>
                    <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ getCorporationStanding(entity) }}
                    </DataCell>
                    <DataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ getAllianceStanding(entity) }}
                    </DataCell>
                </TableRow>

            </SimpleStriped>

        </div>
    </div>
    </transition>

</template>

<script>
import EveImage from "@/Shared/EveImage";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import SimpleStriped from "@/Shared/Layout/Cards/Table/SimpleStriped";
import TableHeader from "@/Shared/Layout/Cards/Table/TableHeader";
import TableRow from "@/Shared/Layout/Cards/Table/TableRow";
import DataCell from "@/Shared/Layout/Cards/Table/DataCell";
import DataHeader from "@/Shared/Layout/Cards/Table/DataHeader";
import axios from "axios";

export default {
    name: "CharacterContactPanel",
    components: {DataHeader, DataCell, TableRow, TableHeader, SimpleStriped, EntityBlock, EveImage},
    props: {
        character: {
            required: true,
            type: Object
        },
        corporation_id: {
            required: true,
            type: Number
        },
        alliance_id: {
            required: false,
        },
    },
    data() {
        return {
            ready: false,
            entities: [],
            entity_affiliations: [],
            enriched_entities: [],
            character_contact: [],
            corporation_contact: [],
            alliance_contact: [],
        }
    },
    methods: {
        resolveIds(ids) {

            return axios.post(this.$route('resolve.ids'), ids)
                .then(res => res.data.forEach((object) => {

                    let resolved_object = {
                        id: object.id,
                        character_id: object.category === 'character' ? object.id : null,
                        corporation_id: object.category === 'corporation' ? object.id : null,
                        alliance_id: object.category === 'alliance' ? object.id : null,
                        name: object.name
                    }

                    this.entities.push(_.omitBy(resolved_object, _.isNil));
                }))
        },
        resolveCharacterAffiliations(ids) {

            return axios.post(this.$route('resolve.character_affiliation'), ids)
                .then(res => res.data.forEach((object) => {
                    object.contact_id = object.character_id
                    this.entity_affiliations.push(object)
                }))
        },
        resolveCorporationAffiliations(corporation) {

            return axios.get(this.$route('resolve.corporation_info', {'corporation_id': corporation.contact_id}))
                .then(res => this.entity_affiliations.push({
                    contact_id: corporation.contact_id,
                    corporation_id: corporation.contact_id,
                    alliance_id: res.alliance_id ?? null
                }))
        },
        resolveAffiliatedIds() {

            //let ids_to_be_resolved = _.omitBy(_.flatten(_.map(this.entity_affiliations, (affiliation) => [affiliation.character_id, affiliation.corporation_id, affiliation.alliance_id])), _.isNil)
            let ids_to_be_resolved = _.chain(this.entity_affiliations)
                .map((affiliation) => [affiliation.character_id, affiliation.corporation_id, affiliation.alliance_id])
                .flatten()
                .omitBy(_.isNil)
                .toArray()
                .uniq()
                .value()

            Promise.allSettled([this.resolveIds(ids_to_be_resolved)])
                .then(() => this.enrichedEntities())
        },
        enrichedEntities() {
            _.map(this.character_contact, (contact) => {

                if(_.isEmpty(contact.character_affiliation))
                    contact.character_affiliation = _.find(this.entity_affiliations, ['contact_id', contact.contact_id])

                let entity = _.find(this.entities, ['id', contact.contact_id])

                if(contact.character_affiliation) {
                    if(contact.character_affiliation.corporation_id && entity)
                        entity.corporation = _.find(this.entities, ['id', contact.character_affiliation.corporation_id])
                }


                /* if(contact?.entity_affiliations.alliance_id)
                     entity.alliance = _.find(this.entities, ['id', contact.entity_affiliations.alliance_id])*/

                this.enriched_entities.push(_.merge(contact, entity))
            })

            this.ready = true
        },
        getLabels(entity) {

            return _.map(entity.labels, (label) => label.label_name)
        },
        getCorporationStanding(entity) {

            if(_.isUndefined(entity.character_affiliation))
                return 'derp'

            let alliance_contact = _.find(this.corporation_contact, ['contact_id', entity.character_affiliation.corporation_id])

            if(alliance_contact)
                return alliance_contact.standing

            let corporation_contact = _.find(this.corporation_contact, ['contact_id', entity.character_affiliation.corporation_id])

            if(corporation_contact)
                return corporation_contact.standing

            let character_contact = _.find(this.corporation_contact, ['contact_id', entity.character_affiliation.character_id])

            if(character_contact)
                return character_contact.standing

            return 0
        },
        getAllianceStanding(entity) {

          if(_.isUndefined(entity.character_affiliation))
              return 'derp'

          let alliance_contact = _.find(this.alliance_contact, ['contact_id', entity.character_affiliation.corporation_id])

          if(alliance_contact)
              return alliance_contact.standing

          let corporation_contact = _.find(this.alliance_contact, ['contact_id', entity.character_affiliation.corporation_id])

          if(corporation_contact)
              return corporation_contact.standing

          let character_contact = _.find(this.alliance_contact, ['contact_id', entity.character_affiliation.character_id])

          if(character_contact)
              return character_contact.standing

            return 0
        },
        async getDetail(id) {
            return this.getUrl(this.$route('character.contacts.detail', id))
        },
        getUrl(url, contactables = []) {
            return axios.get(url)
                .then(({data}) => {

                    let contactable = _.filter([...data.data], (data) => !_.isEmpty(data))

                    contactables.push(...contactable)

                    if (data.links.next)
                        return this.getUrl(data.links.next, contactables)

                    // If no more next pages are present, create contactable
                    let contactable_id = data.meta.entity_id

                    if(contactable_id === this.character.character_id)
                        this.character_contact.push(...contactables)

                    if(contactable_id === this.corporation_id)
                        this.corporation_contact.push(...contactables)

                    if(contactable_id === this.alliance_id)
                        this.alliance_id.push(...contactables)
                })
                .catch(error => console.log(error))
        },
        getContacts() {

            let ids = _.chain([this.character.character_id, this.corporation_id, this.alliance_id]).filter().uniq().value()

            let promises = []

            _.each(ids, (id) => promises.push(this.getDetail(id)))

            Promise.allSettled(promises).then(() => this.getAffiliations())
        },
        handleContactsWithAffiliation() {
            _.each(_.filter(this.character_contact, (contact) => _.isObject(contact.affiliation)), (contact) => {

                    let pushable_object = {
                        contact_id: contact.contact_id,
                        character_id: contact.affiliation.character_id,
                        corporation_id: contact.affiliation.corporation_id,
                        alliance_id: contact.affiliation.alliance_id
                    }

                    this.entity_affiliations.push(_.omitBy(pushable_object, _.isNil))
                })
        },
        getAffiliations() {
            let promises = []

            this.handleContactsWithAffiliation()

            let character_contacs_without_affiliations = _.filter(this.character_contact, (contact) => !_.isObject(contact.affiliation))

            //TODO with char_affiliations eager loaded only request ids which haven't been loaded
            let character_ids = _.filter(_.map(character_contacs_without_affiliations, (contact) => contact.contact_type === 'character' ? contact.contact_id : null))
            if (!_.isEmpty(character_ids))
                promises.push(this.resolveCharacterAffiliations(character_ids))

            let corporations = _.filter(_.map(character_contacs_without_affiliations, (contact) => contact.contact_type === 'corporation' ? contact : null))
            if (!_.isEmpty(corporations))
                corporations.forEach((corporation) => promises.push(this.resolveCorporationAffiliations(corporation)))

            let alliances = _.filter(_.map(character_contacs_without_affiliations, (contact) => contact.contact_type === 'alliance' ? contact : null))
            if (!_.isEmpty(alliances))
                alliances.forEach((contact) => this.entity_affiliations.push({
                    contact_id: contact.contact_id,
                    alliance_id: contact.contact_id
                }))

            Promise.allSettled(promises).then(() => this.resolveAffiliatedIds())
        }

    },
    created() {

        this.getContacts();

    },

}
</script>

<style scoped>

</style>
