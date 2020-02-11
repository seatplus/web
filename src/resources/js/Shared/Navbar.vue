<template>
  <div>
    <b-navbar class="main-header navbar navbar-expand navbar-white navbar-light">

      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <b-nav-item data-widget="pushmenu" href="#">
            <i class="fas fa-bars"/>
        </b-nav-item>

      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">

          <b-spinner variant="primary" label="Spinning" v-if="isLoading"/>

        <b-nav-item :href="route('horizon.index')" v-if="stats.status === 'running'">
            <i class="fas fa-truck-loading"/>
          <b-badge variant="primary">{{ stats.queue_count }}</b-badge>
        </b-nav-item>

        <b-nav-item :href="route('horizon.index')" v-if="stats.status === 'running'">
            <i class="fas fa-bug"/>
          Route:
          <b-badge variant="danger">{{ stats.error_count }}</b-badge>
        </b-nav-item>

        <b-nav-item :href="route('horizon.index')" v-if="stats.status === 'paused'">
            <i class="fas fa-pause"/> Worker is paused
        </b-nav-item>

        <b-nav-item :href="route('horizon.index')" v-if="stats.status === 'inactive'">
            <i class="fas fa-stop"/> Worker has stopped
        </b-nav-item>

      </ul>

    </b-navbar>
    <slot />
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: "Navbar",

    data() {
      return {
        stats: {}
      }
    },
    mounted() {

        this.refreshStats();

        setInterval(() => { this.refreshStats() }, 5000)
    },

    methods: {

      /**
       * Refresh the stats every period of time.
       */
      async refreshStats() {
          Promise.all([

              // Make an ajax request to our server - /queue/status
              await this.loadStats(),

          ]).catch(error => {
              console.log(error);
          })
      },

      /*
       * load stats
       */
      loadStats() {
        return axios
            .get('/queue/status')
            .then( response =>
                this.stats = response.data
            ).catch(error => {
                console.log(error);
            });
      }
    },

    computed : {
      isLoading: function () {

        var obj = this.stats;

        for(var key in obj) {
          if(obj.hasOwnProperty(key))
            return false;
        }
        return true;
      }
    }
  }
</script>

<style scoped>

</style>
