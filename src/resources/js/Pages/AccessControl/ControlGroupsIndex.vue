<template>
    <Layout page="Access Control" page-description="Overview">
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
                        <button @click="toggleModal" type="button" class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700">
                          Create new group
                        </button>
                      </span>
                    </div>
                </div>
                <!-- We use less vertical padding on card headers on desktop than on body sections -->
            </div>

            <!--Content below-->
            <!--Some text explaining what you see here-->

        </div>

        <ControlGroups :roles="this.rolesList" class="mt-8"></ControlGroups>

        <infinite-loading @infinite="infiniteHandler" spinner="waveDots">
            <div slot="no-more"></div>
        </infinite-loading>

        <template v-slot:modal>
            <ModalWithFooter>
                <template v-slot:symbol>
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"  viewBox="0 0 24 24" >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>

                </template>
                <template v-slot:title>
                    <span>Create New Control Group </span>
                </template>
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
                        <inertia-link method="post" :href="$route('acl.create')" :data="{name: createRoleName}" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
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
  import ModalWithFooter from "../../Shared/ModalWithFooter"
  import ControlGroups from "./ControlGroups"
  import InfiniteLoading from "vue-infinite-loading"
  import ManageMembers from "./ManageMembers"

  export default {
      name: "ControlGroupsIndex",
      components: {ManageMembers, ControlGroups, ModalWithFooter, Layout, InfiniteLoading},
      data() {
          return {
              openModal: false,
              createRoleName: '',
              rolesList: [],
              page: 1,
          }
      },
      methods: {
          toggleModal() {
              if(this.openModal)
                  this.managingRole = null

              this.openModal = !this.openModal;
          },
          infiniteHandler($state) {
              axios.get(this.$route('get.acl'), {
                  params: {
                      page: this.page,
                  },
              }).then(({ data }) => {
                  if (data.data.length) {
                      this.page += 1;
                      this.rolesList.push(...data.data);
                      $state.loaded();
                  } else {
                      $state.complete();
                  }
              });
          },
      },
  }
</script>

<style scoped>

</style>
