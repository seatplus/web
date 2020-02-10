<template>
    <Layout>
        <div class="container-fluid">
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
                    <!--<b-col md="4" v-for="role in this.roles.data" :key="role.id">

                    </b-col>-->
                </b-row>
            </b-card>
            <Pagination :collection="roles" class="float-right"/>
        </div>
    </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"
  import { Inertia } from '@inertiajs/inertia'
  import Pagination from "../../Shared/Pagination"
  export default {
      name: "ControlGroups",
      components: {Layout, Pagination},
      props: {
          roles: {
              type: Object,
              required: true
          }
      },
      methods: {
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
          }
      }
  }
</script>

<style scoped>

</style>
