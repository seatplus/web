<template>
  <Layout>
    Dashboard, Translation: {{ $I18n.trans('web::notifications.success') }}
  </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"

  export default {
    name: "Index",
    components: {Layout}
  }
</script>

<style scoped>

</style>