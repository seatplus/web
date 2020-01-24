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
                <b-row>
                    <span v-if="hasNoRoles"> No control groups has been created. Go ahead create one! </span>
                    <b-col md="4" v-for="role in this.roles.data" :key="role.id">
                        <b-card bg-variant="light" style="max-width: 20rem;">

                            <b-card-text>
                                {{role.name}}
                            </b-card-text>

                            <b-button class="float-right" @click="edit(role.id)">
                                Edit
                            </b-button>
                        </b-card>
                    </b-col>
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
          },
          edit: function (role_id) {

              Inertia.visit(route('acl.edit', role_id), {
                  preserveState: true,
              })
          }
      },
      computed: {
          hasNoRoles: function () {
              return _.isEmpty(this.roles.data)
          }
      }
  }
</script>

<style scoped>

</style>
