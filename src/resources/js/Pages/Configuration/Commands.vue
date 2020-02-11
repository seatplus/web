<template>
    <b-card>
        <b-button @click="clearCache">Clear Cache</b-button>
    </b-card>
</template>

<script>
  import axios from 'axios'

  export default {
    name: "Commands",

    methods: {

      clearCache() {
        axios.post(route('cache.clear')).
        catch(function (error) {
          console.log(error)
        })
      }
    }
  }
</script>

<style scoped>

</style>
