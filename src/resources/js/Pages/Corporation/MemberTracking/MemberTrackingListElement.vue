<template>
    <li>
        <a href="#" class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
            <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between ">
                    <div class="text-sm leading-5 font-medium text-indigo-600 truncate inline-flex items-center space-x-2">
                        <EntityBlock :entity="member.character" v-if="member.character"/>
                        <EntityByIdBlock :id="member.character_id" :withSubText="false" v-else />
                    </div>
                    <div class="ml-2 flex-shrink-0 flex">
                        <span v-if="isOk" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            compliant
                        </span>
                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Not compliant
                        </span>
                    </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                        <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ locationName }}
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
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import EveImage from "@/Shared/EveImage";
import Time from "@/Shared/Time";

export default {
    name: "MemberTrackingListElement",
    components: {Time, EveImage, EntityByIdBlock, EntityBlock},
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
