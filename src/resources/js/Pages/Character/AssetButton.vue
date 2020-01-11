<template>
    <b-button variant="link" size="sm" v-b-toggle="asset.content[0].location_id.toString()" v-on:click="toggleIcon()">
        <i :class="isActive ? 'fas fa-minus' : 'fas fa-plus'"></i>
    </b-button>
</template>

<script>
  export default {
    name: "AssetButton",
    props: {
      asset : {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        isActive: false
      }
    },
    methods: {
      toggleIcon() {
        this.isActive = !this.isActive;
      }
    }
  }
</script>

<style scoped>

</style>
