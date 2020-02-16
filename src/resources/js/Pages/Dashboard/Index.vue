<template>
  <Layout page-header="Home" page-description="Dashboard">
      <div class="container-fluid">
          Dashboard, Translation: {{ $I18n.trans('web::notifications.success') }}

          <b-button v-on:click="log">Check Info</b-button>

          <b-button v-on:click="getPersonalAccessToken">Get Personal Access Token</b-button>

          <b-button v-on:click="createPersonalAccessToken">Create Personal Access Token</b-button>
      </div>



  </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout";
  import axios from 'axios';

  export default {
    name: "Index",

    components: {Layout},

    methods: {

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
