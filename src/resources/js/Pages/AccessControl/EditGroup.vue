<template>
    <Layout :active-sidebar-element="activeSidebarElement">
        <template v-slot:title>
            <PageHeader :breadcrumbs="[{name: 'Control Group', route: 'acl.groups'}]">
                Access Control Groups
                <template v-slot:primary>
                    <HeaderButton @click="store">
                        Save
                    </HeaderButton>
                </template>

            </PageHeader>
        </template>

        <div class="bg-white overflow-hidden shadow rounded-lg mb-3">
            <div class="px-4 py-5 sm:p-6">

                <div>
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Settings
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            First set a name and which permission should be applied to the access control group
                        </p>
                    </div>
                    <div class="mt-6 sm:mt-5">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="roleName" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                                Access control group name
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div class="max-w-xs rounded-md shadow-sm">
                                    <input id="roleName" v-model="roleName" class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5" :placeholder="roleName">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="permissions" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                                Permissions
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div id="permissions" class="sm:grid sm:grid-cols-2 sm:gap-4">
                                    <div v-for="permission in available_permissions" >
                                        <label  class="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="permission"
                                                v-model="selectedPermissions"
                                                class="form-checkbox"
                                            >
                                            <span class="ml-2">{{ permission }}</span>
                                        </label>
                                    </div>
                                </div>
                                <!--<Permissions id="permissions" v-model="selectedPermissions" :available-permissions="available_permissions" />-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Affiliations v-model="selectedAffiliations" :affiliations="affiliations"/>

    </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"
  import { Inertia } from '@inertiajs/inertia'
  import List from "../../Shared/List"
  import ListElement from "../../Shared/ListElement"
  import EveImage from "../../Shared/EveImage"
  import SeatPlusSelect from "../../Shared/SeatPlusSelect"
  import AddAffiliations from "./AddAffiliations"
  import Affiliations from "./Affiliations"
  import PageHeader from "../../Shared/Layout/PageHeader"
  import HeaderButton from "../../Shared/Layout/HeaderButton"
  export default {
      name: "EditGroup",
      components: {
          HeaderButton,
          PageHeader, Affiliations, AddAffiliations, SeatPlusSelect, EveImage, ListElement, List, Layout},
      data () {
          return {
              roleName: '',
              selectedPermissions: [],
              selectedAffiliations: this.affiliations,
              isDirty: false
          }
      },
      props: {
          role: {
              type: Object,
              required: true
          },
          affiliations: {
              type: Array,
              required: true
          },
          permissions: {
              type: Array,
              required: true
          },
          available_permissions: {
              type: Array,
              required: true
          }
      },
      methods: {
          getOptions() {
              return {
                  characters: this.available_characters,
                  corporations: this.available_corporations,
                  alliances: this.available_alliances
              }
          },
          getPermissionOptions: function () {
              let permissionOption = []

              _.each(this.available_permissions, permission => permissionOption.push({name: permission}))

              return permissionOption;
          },
          store: function () {

              let data = {
                  allowed: this.selectedAffiliations.allowed,
                  inverse: this.selectedAffiliations.inverse,
                  forbidden: this.selectedAffiliations.forbidden,
                  roleName: this.roleName,
                  permissions: this.selectedPermissions
              }

              Inertia.post(window.location.href, data, {
                  replace: false,
                  preserveState: false,
                  preserveScroll: false,
                  only: [],
              })
          },
          findCharacterName: function (character_id) {

              let character = _.find(this.available_characters, function (available_character) {
                  return available_character.character_id === character_id
              })

              return character.name
          },
          buildAffiliatedCharacterIds: function () {

              _.forEach(this.affiliations, affiliation => this.buildAffiliatedEntries(affiliation.type,{
                  character_id: affiliation.character_id,
                  name: this.findCharacterName(affiliation.character_id)
              }))
          },
          buildAffiliatedEntries: function (type, data) {
              if(type === 'allowed')
                  this.allowed.push(data)
              else if(type === 'inverse')
                  this.inverse.push(data)
              else if(type === 'forbidden')
                  this.forbidden.push(data)
          },
          setIsDirty: function () {
              this.isDirty = true
          }
      },
      computed: {
          activeSidebarElement: function () {
              return route('acl.groups').url()
          }
      },
      mounted: function () {

          this.roleName = this.role.name

          _.each(this.permissions, permission =>
              this.selectedPermissions.push(permission.name)
          )
      },
  }
</script>

<style scoped>

</style>
