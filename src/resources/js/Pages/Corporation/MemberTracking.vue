<template>
    <Layout page="Corporation" page-description="Member Tracking" :dispatch_transfer_object="dispatch_transfer_object">

        <template v-slot:title>
            <PageHeader>
                Corporation Member Tracking
                <template v-slot:primary>
                    <HeaderButton @click="openSlideOver">
                        Update
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <div v-for="entry in member_tracking" class="bg-white overflow-hidden shadow rounded-lg">
            <!--Header-->
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <!-- Content goes here -->
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="ml-4 mt-2 inline-flex items-center space-x-4">
                        <EveImage :object="entry.corporation" />
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ entry.corporation.name }}
                        </h3>
                    </div>
                </div>
                <!-- We use less vertical padding on card headers on desktop than on body sections -->
            </div>

            <!--Content below-->

            <!--List for small devices-->
            <div class="bg-white shadow overflow-hidden sm:hidden sm:rounded-md">
                <ul class="divide-y divide-cool-gray-200">
                    <li v-for="member in entry.members">
                        <a href="#" class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between ">
                                    <div class="text-sm leading-5 font-medium text-indigo-600 truncate inline-flex items-center space-x-2">
                                        <EveImage :object="{character_id: member.character_id}" :size="256" tailwind_class="h-6 w-6 rounded-full" />
                                        <span v-if="hasCharacter(member)">{{ member.character.name }}</span>
                                        <span v-else> {{ getCharacterName(member) }}</span>
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span v-if="missingScopes(member)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Not compliant
                                        </span>
                                        <span v-if="missingTokens(member)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            No Token
                                        </span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            compliant
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ getLocationName(member) }}
                                        </div>
                                        <div class="mt-2 mr-6 flex items-center text-sm leading-5 text-gray-500 space-x-1.5">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="flex-shrink-0 h-5 w-5 text-gray-400">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                            </svg>
                                            <EveImage :object="{type_id: member.ship_type_id}" :size="256" tailwind_class="h-5 w-5 rounded-full" />
                                            <span v-if="member.ship">{{ member.ship.name }} </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="mr-1.5 w-5 h-5 text-gray-400">
                                            <path fill-rule="evenodd" d="M6 3a1 1 0 011-1h.01a1 1 0 010 2H7a1 1 0 01-1-1zm2 3a1 1 0 00-2 0v1a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2V6a1 1 0 10-2 0v1h-1V6a1 1 0 10-2 0v1H8V6zm10 8.868a3.704 3.704 0 01-4.055-.036 1.704 1.704 0 00-1.89 0 3.704 3.704 0 01-4.11 0 1.704 1.704 0 00-1.89 0A3.704 3.704 0 012 14.868V17a1 1 0 001 1h14a1 1 0 001-1v-2.132zM9 3a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm3 0a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span> Joined <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.start_date" /></span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                        <span> Last Login <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.logon_date" /></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!--Table for medium and above-->
            <div class="flex flex-col hidden sm:block">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Token
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Last Location
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Ship
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Last Login
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="(member,index) in entry.members" :class="index%2 ? 'bg-gray-50' :'bg-white'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-900">
                                    <div class="flex-shrink-0">
                                        <svg v-if="missingScopes(member) || missingTokens(member)" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 font-medium text-gray-500">
                                    <div class="inline-flex items-center space-x-4">
                                        <EveImage :object="{character_id: member.character_id}" :size="256" tailwind_class="h-6 w-6 rounded-full" />
                                        <span v-if="hasCharacter(member)">{{ member.character.name }}</span>
                                        <span v-else> {{ getCharacterName(member) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 sm:max-w-xs md:max-w-sm truncate">
                                    {{ getLocationName(member) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 lg:">
                                    <div class="inline-flex items-center space-x-4">
                                        <EveImage :object="{type_id: member.ship_type_id}" :size="256" tailwind_class="h-6 w-6 rounded-full" />
                                        <span v-if="member.ship">{{ member.ship.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500">
                                    <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.start_date" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500">
                                    <Time format="YYYY-MM-DD HH:mm:ss" :timestamp="member.logon_date" />
                                </td>
                            </tr>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <template v-slot:slideOver>
            <SlideOver>
                <template v-slot:title>Dispatch Update Job</template>
                <DispatchUpdate :dispatch_transfer_object="dispatch_transfer_object" />
            </SlideOver>
        </template>
    </Layout>
</template>

<script>
import Layout from "../../Shared/Layout"
import EveImage from "../../Shared/EveImage"
import axios from 'axios';
import dayjs from 'dayjs'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import Time from "../../Shared/Time"
import PageHeader from "../../Shared/Layout/PageHeader"
import HeaderButton from "../../Shared/Layout/HeaderButton"
import SlideOver from "../../Shared/Layout/SlideOver"
import DispatchUpdate from "../../Shared/DispatchUpdate"

dayjs.extend(customParseFormat);

export default {
    name: "MemberTracking",
    components: {DispatchUpdate, SlideOver, HeaderButton, PageHeader, Time, EveImage, Layout},
    props: {
        member_tracking: {
            required: true
        },
        dispatch_transfer_object: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            // TODO: Add Required Scopes for corporation component
            requiredScopes: ['esi-characters.read_corporation_roles.v1', 'esi-corporations.track_members.v1'],
            unknownIds: [],
            resolvedIds: [],
        }
    },
    methods: {
        getLocationName(member) {
            return member.location ? member.location.locatable.name : 'Unknown Location'
        },
        getCharacterName: function (member) {

            let entry = _.find(this.resolvedIds, {id: member.character_id})

            return entry ? entry.name : ''
        },
        resolveUnknownIds: _.debounce( function () {

            //TODO: filter already resolved ids

            axios
                .post(this.$route('resolve.ids'), this.unknownIds)
                .then(res => res.data.forEach( (object) => {
                    this.resolvedIds.push({id: object.id, name: object.name})
                }))
        }, 200),
        hasCharacter(member) {

            let hasNoCharacter = _.isNull(member.character)

            if(hasNoCharacter && this.unknownIds.indexOf(member.character_id) === -1)
                this.unknownIds.push(member.character_id)

            return !hasNoCharacter
        },
        getMsRepresentation(formated_time) {

            console.log(dayjs(formated_time, 'YYYY-MM-DD HH:mm:ss'));

            return dayjs(formated_time, 'YYYY-MM-DD HH:mm:ss').valueOf()
        },
        missingTokens(member) {
            return _.isNull(member.character.refresh_token)
        },
        missingScopes(member) {

            return !_.isEmpty(member.missing_sso_scopes)
        },
        openSlideOver() {
            this.$eventBus.$emit('open-slideOver', 'update');
        }
    },
    computed: {
        resolvedNames() {
            let resolvedIds = {}

            this.unknownIds.forEach((id) => resolvedIds[id] = this.resolvedIds[id] ?? '')

            return resolvedIds
        }
    },
    watch: {
        unknownIds(newValues) {
            this.resolveUnknownIds()
        }
    }
}
</script>

<style scoped>

</style>
