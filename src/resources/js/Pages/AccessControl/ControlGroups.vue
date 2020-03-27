<template>
    <Layout page="Access Control" page-description="Overview">
        <!--<div class="container-fluid">
            <b-card>
                <b-row>

                    <b-col md="9">
                        <b-form-group label="Search for a group">
                            <b-form-input placeholder="Search"/>
                        </b-form-group>
                    </b-col>

                    <b-col md="3">
                        <b-form-group label="Create a group">
                            <b-button block variant="primary" @click="create">
                                Create
                            </b-button>
                        </b-form-group>
                    </b-col>

                </b-row>
            </b-card>

            <b-card>
                <div class="d-flex flex-row justify-content-around" v-for="roles in chunkedRoles">
                    <b-card bg-variant="light"  v-for="role in roles" :key="role.id" class="flex-fill">

                        <b-card-title>
                            {{role.name}}
                        </b-card-title>

                        <b-card-text>Members</b-card-text>

                        <b-button-group>
                            <inertia-link :href="route('acl.join')"
                                          class="btn btn-success"
                                          method="post"
                                          :data="{ role_id: role.id }"
                            >
                                Join
                            </inertia-link>
                            <inertia-link :href="route('acl.manage', role.id)" class="btn btn-warning">
                                Manage Members
                            </inertia-link>
                        </b-button-group>


                        <b-button-group class="float-right">
                            <inertia-link :href="route('acl.edit', role.id)" class="btn btn-primary">
                                Edit
                            </inertia-link>
                            <inertia-link
                                :href="route('acl.delete')"
                                method="delete"
                                :data="{ role_id: role.id }"
                                class="btn btn-danger"
                            >
                                Delete
                            </inertia-link>
                        </b-button-group>

                    </b-card>
                </div>
                <b-row>
                    <span v-if="hasNoRoles"> No control groups has been created. Go ahead create one! </span>
                    &lt;!&ndash;<b-col md="4" v-for="role in this.roles.data" :key="role.id">

                    </b-col>&ndash;&gt;
                </b-row>
            </b-card>

        </div>-->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <!--Header-->
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <!-- Content goes here -->
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-no-wrap">
                    <div class="ml-4 mt-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Access Control Groups
                        </h3>
                    </div>
                    <div class="ml-4 mt-2 flex-shrink-0">
                      <span class="inline-flex rounded-md shadow-sm">
                        <button @click="toggleCreateModal(true)" type="button" class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700">
                          Create new group
                        </button>
                      </span>
                    </div>
                </div>
                <!-- We use less vertical padding on card headers on desktop than on body sections -->
            </div>

            <!--Content below-->
            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full overflow-hidden"> <!-- shadow sm:rounded-lg border-b border-gray-200-->
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Members
                                </th>
                                <!--<th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>-->
                                <!--<th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>-->
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"><!--Manage--></th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"><!--Join--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr :class="{'bg-white' : index % 2 === 0, 'bg-gray-50': index % 2 !== 0}" ref="tr" v-for="(role, index) in roles.data">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                    {{role.name}} {{index}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    <AvatarGroupBottomTop :objects="getRoleMembers(role.users)" :random="true"/>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    <DropdownWithIcons :index="index"  v-on:change="toggleHelperRow" >
                                        <div class="rounded-md bg-white shadow-xs">
                                            <div class="py-1">
                                                <inertia-link :href="route('acl.edit', role.id)" class="group flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Edit
                                                </inertia-link>
                                                <inertia-link :href="route('acl.manage', role.id)" class="group flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                                    <svg fill="currentColor" viewBox="0 0 20 20" class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500">
                                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                                    </svg>
                                                    Manage
                                                </inertia-link>
                                                <!--<a href="#" class="group flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h6a2 2 0 012 2H5v8a2 2 0 01-2-2V5zm6 2a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Duplicate
                                                </a>-->
                                            </div>
                                            <div class="border-t border-gray-100"></div>
                                            <div class="py-1">
                                                <inertia-link
                                                    :href="route('acl.delete')" method="delete" :data="{ role_id: role.id }"
                                                    class="group flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                                                >
                                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Delete
                                                </inertia-link>
                                            </div>
                                        </div>
                                    </DropdownWithIcons>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <!--<SimpleToggle :value="dispatch" v-on:change="toggleDispatch" />-->
                                    <!--<a href="#" class="text-right text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>-->
                                    <!--<inertia-link :href="route('acl.join')"
                                                  class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline"
                                                  method="post"
                                                  :data="{ role_id: role.id }"
                                    >
                                        Join
                                    </inertia-link>-->
                                </td>
                            </tr>
                            <tr v-show="rowHeight > 0" :class="'bg-grey-500'">
                                <td colspan="4" :height="rowHeight" class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium" />
                            </tr>
                            <!--<tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                    Bernard Lane
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Director, Human Resources
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    bernardlane@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Owner
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                    Bernard Lane
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Director, Human Resources
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    bernardlane@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Owner
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                    Bernard Lane
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Director, Human Resources
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    bernardlane@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Owner
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                    Bernard Lane
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Director, Human Resources
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    bernardlane@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                    Owner
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                            </tr>-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
                <!-- Content goes here -->
                <Pagination :collection="roles" />
                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
            </div>
        </div>

        <template v-slot:modal>
            <ModalWithFooter :open="openCreateModal" @change="toggleCreateModal">
                <template v-slot:symbol>
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"  viewBox="0 0 24 24" >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </template>
                <template v-slot:title>Create New Control Group</template>
                <template v-slot:description>
                    <div>
                        <label for="role" class="block text-sm font-medium leading-5 text-gray-700">Control Group</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input id="role" class="form-input block w-full sm:text-sm sm:leading-5" placeholder="Some name" v-model="createRoleName"/>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Chose a descriptive name for the group to be created</p>
                    </div>
                </template>
                <template v-slot:buttons>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <inertia-link method="post" :href="route('acl.create')" :data="{name: createRoleName}" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Create
                        </inertia-link>
                    </span>
                </template>
            </ModalWithFooter>
        </template>
    </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"
  import { Inertia } from '@inertiajs/inertia'
  import Pagination from "../../Shared/Pagination"
  import SimpleToggle from "../../Shared/SimpleToggle"
  import ModalWithFooter from "../../Shared/ModalWithFooter"
  import AvatarGroupBottomTop from "../../Shared/AvatarGroupBottomTop"
  import DropdownWithIcons from "../../Shared/DropdownWithIcons"
  export default {
      name: "ControlGroups",
      components: {DropdownWithIcons, AvatarGroupBottomTop, ModalWithFooter, SimpleToggle, Layout, Pagination},
      data() {
          return {
              dispatch: false,
              openCreateModal: false,
              rowHeight: 0,
              indexDropdown: 0,
              createRoleName: ''
          }
      },
      props: {
          roles: {
              type: Object,
              required: true
          }
      },
      methods: {
          toggleDispatch(value) {
              this.dispatch = value;
          },
          toggleCreateModal(value) {
              this.openCreateModal = value;
          },
          getRoleMembers(users) {
              return _.map(users, function (user) {
                  return user.main_character
              })
          },
          toggleHelperRow(object) {

              console.log(object)

              this.rowHeight = object.dropdownHeight - (_.size(this.roles.data) - (object.index + 1)) * this.evaluateRowHeight()
          },
          evaluateRowHeight() {
              return this.rolesLength === 0 ? 0 : this.$refs.tr[0].clientHeight
          },
          create: async function () {

              const {value: ipAddress} = await Swal.fire({
                  title: 'Create control group',
                  text           : 'Enter the groups name',
                  input           : 'text',
                  icon : 'question',
                  showCancelButton: true,
                  inputValidator  : (value) => {
                      if (!value) {
                          return 'You need to write something!'
                      }
                  }
              })

              if (ipAddress) {
                  //Swal.fire(`Your IP address is ${ipAddress}`)

                  let request = {
                      'input': ipAddress
                  }

                  Inertia.post(route('acl.create'), request, {
                      replace: false,
                      preserveState: true,
                      preserveScroll: false,
                      only: [],
                  })
              }
          }
      },
      computed: {
          hasNoRoles: function () {
              return _.isEmpty(this.roles.data)
          },
          chunkedRoles: function () {
              return _.chunk(this.roles.data, 3)
          },
          rolesLength: function () {
              return this.roles.data.length
          }
      }
  }
</script>

<style scoped>

</style>
