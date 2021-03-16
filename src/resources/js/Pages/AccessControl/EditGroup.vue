<template>
  <div class="space-y-3">
    <PageHeader :breadcrumbs="breadcrumbs">
      Access Control Groups
      <template #primary>
        <HeaderButton
          :secondary="true"
          @click="remove"
        >
          Delete
        </HeaderButton>
        <HeaderButton @click="store">
          Save
        </HeaderButton>
      </template>
    </PageHeader>

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
              <label
                for="roleName"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Access control group name
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="roleName"
                    v-model="form.roleName"
                    class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    :placeholder="form.roleName"
                  >
                </div>
              </div>
            </div>

            <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
              <label
                for="permissions"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Permissions
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div
                  id="permissions"
                  class="sm:grid sm:grid-cols-2 sm:gap-4"
                >
                  <div
                    v-for="permission in available_permissions"
                    :key="permission"
                  >
                    <label class="inline-flex items-center">
                      <input
                        v-model="form.permissions"
                        type="checkbox"
                        :value="permission"
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

    <EditSettings
      v-model:allowed="form.allowed"
      v-model:inverse="form.inverse"
      v-model:forbidden="form.forbidden"
      :existing-affiliations="existing_affiliations"
    />
  </div>
</template>

<script>
  import { Inertia } from '@inertiajs/inertia'
  import PageHeader from "@/Shared/Layout/PageHeader"
  import HeaderButton from "@/Shared/Layout/HeaderButton"
  import Layout from "@/Shared/SidebarLayout/Layout";
  import EditSettings from "./Edit/EditSettings";
  import {useForm} from "@inertiajs/inertia-vue3/src";
  export default {
    name: "EditGroup",
    components: {
      EditSettings,
      HeaderButton,
      PageHeader
    },
    layout: (h, page) => h(Layout, { activeSidebarElement: page.props.activeSidebarElement }, [page]),
      props: {
          role: {
              type: Object,
              required: true
          },
        existing_affiliations: {
              type: Object,
              required: true
          },
          permissions: {
              type: Array,
              required: true
          },
          available_permissions: {
              type: Array,
              required: true
          },
        activeSidebarElement: {
            type: String,
          required: true
        }
      },
    setup(props) {

      const form = useForm({
        roleName: props.role.name,
        permissions: _.map(props.permissions, (permission) => permission.name),
        allowed: props.existing_affiliations.allowed ?? [],
        inverse: props.existing_affiliations.inverse ?? [],
        forbidden: props.existing_affiliations.forbidden ?? [],
      })

      return { form }
    },
      data () {
          return {
              isDirty: false,
            selectedAffiliations: this.affiliations,
              breadcrumbs: [
                  {
                      name: 'Control Group',
                      route: this.$route('acl.groups')
                  }
              ]
          }
      },
      mounted: function () {

          _.each(this.permissions, permission =>
              this.form.permissions.push(permission.name)
          )
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
          store() {

            return this.form.post(window.location.href)
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
          },
          remove() {

              this.$inertia.delete(this.$route('acl.delete', this.role.id), {
                  replace: false,
                  preserveState: false,
                  preserveScroll: false,
                  only: [],
              })
          }
      },
  }
</script>

<style scoped>

</style>
