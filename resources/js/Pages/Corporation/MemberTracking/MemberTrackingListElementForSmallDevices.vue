<template>
  <li class="sm:hidden ">
    <div class="hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 transition duration-150 ease-in-out">
      <div class="px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between ">
          <div class="text-sm leading-5 font-medium text-indigo-600 truncate inline-flex items-center space-x-2">
            <EntityBlock
              v-if="member.character"
              :entity="member.character"
            />
            <EntityByIdBlock
              v-else
              :id="member.character_id"
              :with-sub-text="false"
            />
          </div>
          <div class="ml-2 shrink-0 flex">
            <span
              v-if="isOk"
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800"
            >
              compliant
            </span>
            <span
              v-else
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
            >
              Not compliant
            </span>
          </div>
        </div>
        <div class="mt-2 sm:flex sm:justify-between">
          <div class="sm:flex">
            <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
              <MapPinIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
              {{ locationName }}
            </div>
            <div class="mt-2 mr-6 flex items-center text-sm leading-5 text-gray-500 space-x-1.5">
              <RocketLaunchIcon class="shrink-0 h-5 w-5 text-gray-400" />
              <EveImage
                :object="{type_id: member.ship_type_id}"
                :size="256"
                tailwind_class="h-5 w-5 rounded-full"
              />
              <span v-if="member.ship">{{ member.ship.name }} </span>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
            <CalendarIcon class="mr-1.5 w-5 h-5 text-gray-400" />
            <span> Joined <Time
              format="YYYY-MM-DD HH:mm:ss"
              :timestamp="member.start_date"
            /></span>
          </div>
          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
            <ClockIcon class="shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
            <span> Last Login <Time
              format="YYYY-MM-DD HH:mm:ss"
              :timestamp="member.logon_date"
            /></span>
          </div>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import EveImage from "@/Shared/EveImage.vue";
import Time from "@/Shared/Time.vue";
import { CalendarIcon, ClockIcon, MapPinIcon, RocketLaunchIcon } from '@heroicons/vue/20/solid'

export default {
    name: "MemberTrackingListElementForSmallDevices",
    components: {Time, EveImage, EntityByIdBlock, EntityBlock, CalendarIcon, ClockIcon, MapPinIcon, RocketLaunchIcon},
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
    },
    methods: {
        has(string) {
            return _.has(this.member, string)
        }
    }
}
</script>

<style scoped>

</style>
