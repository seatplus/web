<template>
  <Layout page="Home">
      <!--<div class="container-fluid">
          Dashboard, Translation: {{ $I18n.trans('web::notifications.success') }}

          <b-button v-on:click="log">Check Info</b-button>

          <b-button v-on:click="getPersonalAccessToken">Get Personal Access Token</b-button>

          <b-button v-on:click="createPersonalAccessToken">Create Personal Access Token</b-button>
      </div>-->
      <span class="inline-flex rounded-md shadow-sm">
  <button @click="emmitEvent" type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
    Test Notification
  </button>
</span>


  </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout";
  import axios from 'axios';

  export default {
    name: "Index",

    components: {Layout},

    methods: {
        emmitEvent() {
            this.$eventBus.$emit('notification', {
                link1: {
                    route: 'auth.login',
                    text: 'test'
                }
            })
        },

      log: function () {
        axios.get('/eveapi/character/info')
            .then(response => {
              console.log(response.data);
            })
      },

      getPersonalAccessToken: function () {
        axios.get('/oauth/personal-access-tokens')
            .then(response => {
              console.log(response.data);
            });
      },

      createPersonalAccessToken: function () {
        const data = {
          name: 'Token Name',
          scopes: []
        };

        axios.post('/oauth/personal-access-tokens', data)
            .then(response => {
              console.log(response.data.accessToken);
            })
            .catch (response => {
              // List errors on response...
            });
      }
    }
  }
</script>

<style scoped>

</style>
