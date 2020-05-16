<template>
    <Layout page="Access Control Groups" :active-sidebar-element="activeSidebarElement">
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

            <b-card-sub-title>

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

        Setup the access group and define what anyone in this group should be able to achieve for which asset. F.e. if this is your recruiter access contol group you would define f.e. that a recruiter should have access to assets of characters in any but (inverse) a specific corporation. Note: you will be able to assign the access control group to users in another step.

        <div class="m-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                            Boost your conversion rate
                        </h3>
                        <p class="mt-3 text-base leading-6 text-gray-500">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.
                        </p>
                    </div>
                    <div class="mt-6 flex items-center">
                        <div class="flex-shrink-0">
                            <a href="#">
                                <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                            </a>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 font-medium text-gray-900">
                                <a href="#" class="hover:underline">
                                    Roel Aufderhar
                                </a>
                            </p>
                            <div class="flex text-sm leading-5 text-gray-500">
                                <time datetime="2020-03-16">
                                    Mar 16, 2020
                                </time>
                                <span class="mx-1">&middot;</span>
                                <span>6 min read</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-shrink-0">
                    <img class="h-48 w-full object-cover" src="https://images.unsplash.com/photo-1547586696-ea22b4d4235d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1650&q=80" alt="" />
                </div>
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <p class="text-sm leading-5 font-medium text-indigo-600">
                            <a href="#" class="hover:underline">
                                Video
                            </a>
                        </p>
                        <a href="#" class="block">
                            <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                How to use search engine optimization to drive sales
                            </h3>
                            <p class="mt-3 text-base leading-6 text-gray-500">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit facilis asperiores porro quaerat doloribus, eveniet dolore. Adipisci tempora aut inventore optio animi., tempore temporibus quo laudantium.
                            </p>
                        </a>
                    </div>
                    <div class="mt-6 flex items-center">
                        <div class="flex-shrink-0">
                            <a href="#">
                                <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                            </a>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 font-medium text-gray-900">
                                <a href="#" class="hover:underline">
                                    Brenna Goyette
                                </a>
                            </p>
                            <div class="flex text-sm leading-5 text-gray-500">
                                <time datetime="2020-03-16">
                                    Mar 16, 2020
                                </time>
                                <span class="mx-1">
                  &middot;
                </span>
                                <span>
                  6 min read
                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-shrink-0">
                    <img class="h-48 w-full object-cover" src="https://images.unsplash.com/photo-1492724441997-5dc865305da7?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1650&q=80" alt="" />
                </div>
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <p class="text-sm leading-5 font-medium text-indigo-600">
                            <a href="#" class="hover:underline"> Case Study</a>
                        </p>
                        <a href="#" class="block">
                            <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                Improve your customer experience
                            </h3>
                            <p class="mt-3 text-base leading-6 text-gray-500">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint harum rerum voluptatem quo recusandae magni placeat saepe molestiae, sed excepturi cumque corporis perferendis hic.
                            </p>
                        </a>
                    </div>
                    <div class="mt-6 flex items-center">
                        <div class="flex-shrink-0">
                            <a href="#">
                                <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                            </a>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 font-medium text-gray-900">
                                <a href="#" class="hover:underline">
                                    Daniela Metz
                                </a>
                            </p>
                            <div class="flex text-sm leading-5 text-gray-500">
                                <time datetime="2020-03-16">
                                    Mar 16, 2020
                                </time>
                                <span class="mx-1">
                  &middot;
                </span>
                                <span>
                  6 min read
                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="relative bg-gray-50 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
            <div class="absolute inset-0">
                <div class="bg-white h-1/3 sm:h-2/3"></div>
            </div>
            <div class="relative max-w-7xl mx-auto">
                <div class="text-center">
                    <h2 class="text-3xl leading-9 tracking-tight font-extrabold text-gray-900 sm:text-4xl sm:leading-10">
                        From the blog
                    </h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl leading-7 text-gray-500 sm:mt-4">
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ipsa libero labore natus atque, ducimus sed.
                    </p>
                </div>

            </div>
        </div>-->

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
      computed: {
          activeSidebarElement: function () {
              return route('acl.groups').url()
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
