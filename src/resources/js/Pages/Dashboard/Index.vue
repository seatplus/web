<template>
  <Layout page="Home">

      <template v-slot:title>
          <PageHeader>
              Home
          </PageHeader>
      </template>

      <Characters :characters="characters" :enlistments="characterEnlistments" class="mb-4"/>

      <Enlistments v-if="hasCorporationEnlistments" :enlistments="corporationEnlistments" :application="user_application" class="mb-4"/>

      <span class="inline-flex rounded-md shadow-sm">
        <button @click="emmitEvent" type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition ease-in-out duration-150">
            Test Notification
        </button>
      </span>

  </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout";
  import axios from 'axios';
  import PageHeader from "../../Shared/Layout/PageHeader"
  import Enlistments from "./Enlistments"
  import Characters from "./Characters"

  export default {
      name: "Index",
      components: {Characters, Enlistments, PageHeader, Layout},
      props: {
          characters: {
              type: Array
          },
          user_application: {
              required: true
          }
      },
      data() {
          return {
              enlistments: []
          }
      },
      methods: {
          emmitEvent() {
              /*TODO this.$eventBus.$emit('notification', {
                  type: 'success',
              })*/
          },
          async getEnlistments() {
              axios.get(this.$route('list.open.enlistments'))
                  .then((result) => this.enlistments.push(...result.data))
          },

      },
      created() {
          this.getEnlistments()
      },
      computed: {
          characterEnlistments() {
              return _.filter(this.enlistments, (enlistment) => enlistment.type === 'character')
          },
          corporationEnlistments() {

              return _.filter(this.enlistments, (enlistment) => enlistment.type === 'user')
          },
          hasCorporationEnlistments() {
              return ! _.isEmpty(this.corporationEnlistments)
          }
      }
  }
</script>

<style scoped>

</style>
