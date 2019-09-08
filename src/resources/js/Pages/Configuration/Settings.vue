<template>
  <Layout>
    Settings
      <Commands />
  </Layout>
</template>

<script>
  import Layout from "@/Shared/Layout"
  import Commands from "@/Pages/Configuration/Commands"

  export default {
    name: "Settings",
    components: {Layout, Commands}
  }
</script>

<style scoped>

</style>
