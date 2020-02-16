<template>
    <Layout page-header="Access Control Groups" page-description="Edit Group" :active-sidebar-element="route('acl.groups')">
        <b-card>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <b-form-group label="Access control group name">
                            <b-form-input v-model="roleName" @update="setIsDirty"/>
                        </b-form-group>
                    </div>
                </div>
            </div>
        </b-card>
        <b-card>
            <b-card-title class="mr-3">Access control settings</b-card-title>
            <b-card-sub-title>
                Setup the access group and define what anyone in this group should be able to achieve for which asset. F.e. if this is your recruiter access contol group you would define f.e. that a recruiter should have access to assets of characters in any but (inverse) a specific corporation. Note: you will be able to assign the access control group to users in another step.
            </b-card-sub-title>
            <b-card-text>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <b-form-group label="Permission">
                                <multiselect @select="setIsDirty" @remove="setIsDirty" v-model="selectedPermissions" :options="getPermissionOptions()" :multiple="true" placeholder="Type to search" track-by="name" label="name"><span slot="noResult">Oops! No elements found. Consider changing the search query.</span></multiselect>
                            </b-form-group>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <b-form-group label="Allowed">
                                <multiselect @select="setIsDirty" @remove="setIsDirty" v-model="allowed" :options="getOptions()" :multiple="true" group-values="options" group-label="category" :group-select="true" placeholder="Type to search" track-by="name" label="name"><span slot="noResult">Oops! No elements found. Consider changing the search query.</span></multiselect>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group label="Inverse">
                                <multiselect @select="setIsDirty" @remove="setIsDirty"  v-model="inverse" :options="getOptions()" :multiple="true" group-values="options" group-label="category" :group-select="true" placeholder="Type to search" track-by="name" label="name"><span slot="noResult">Oops! No elements found. Consider changing the search query.</span></multiselect>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group label="Forbidden">
                                <multiselect @select="setIsDirty" @remove="setIsDirty" v-model="forbidden" :options="getOptions()" :multiple="true" group-values="options" group-label="category" :group-select="true" placeholder="Type to search" track-by="name" label="name"><span slot="noResult">Oops! No elements found. Consider changing the search query.</span></multiselect>
                            </b-form-group>
                        </div>
                    </div>
                </div>
            </b-card-text>

        </b-card>

        <b-form-group label="" >
            <b-button block variant="success" @click="store" v-if="isDirty">Update</b-button>
        </b-form-group>

    </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"
  import Multiselect from 'vue-multiselect'
  import axios from 'axios'
  import { Inertia } from '@inertiajs/inertia'
  export default {
      name: "EditGroup",
      components: {Layout, Multiselect},
      data () {
          return {
              available_characters: null,
              available_corporations: null,
              available_alliances: null,
              allowed: [],
              inverse: [],
              forbidden: [],
              roleName: '',
              selectedPermissions: [],
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
          getOptions: function () {

              return [
                  {
                      category: 'Characters',
                      options:  this.available_characters
                  },
                  {
                      category: 'Corporations',
                      options: this.available_corporations
                  },
                  {
                      category: 'Alliances',
                      options: this.available_alliances
                  }
              ]
          },
          getPermissionOptions: function () {
              let permissionOption = []

              _.each(this.available_permissions, permission => permissionOption.push({name: permission}))

              return permissionOption;
          },
          getInfo: function (url, info = []) {
              return axios
                      .get(url)
                      .then(response => {

                          response.data.data.forEach(object => info.push(object))

                          if (_.isNull(response.data.links.next))
                              return info

                          return this.getInfo(response.data.links.next, info)
                      })
                      .catch(error => console.log(error))
          },
          store: function () {

              let data = {
                  allowed: this.allowed,
                  inverse: this.inverse,
                  forbidden: this.forbidden,
                  roleName: this.roleName,
                  permissions: this.selectedPermissions
              }

              Inertia.post(window.location.href, data, {
                  replace: false,
                  preserveState: true,
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
      mounted: function () {

          this.getInfo(route('get.character_info')).then(info => this.available_characters = info).then(info => this.buildAffiliatedCharacterIds())

          this.getInfo(route('get.corporation_info')).then(info => this.available_corporations = info)

          this.getInfo(route('get.alliance_info')).then(info => this.available_alliances = info)

          this.roleName = this.role.name

          _.each(this.permissions, permission =>
              this.selectedPermissions.push({name: permission.name})
          )
      },
  }
</script>

<style scoped>

</style>
