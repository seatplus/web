<template>
  <div>

    <b-alert v-if="Object.keys($page.errors).length > 0" show variant="danger" dismissible>
      <h4 class="alert-heading">{{ $page.translation.error }}</h4>
      <span v-for="error in $page.errors">{{ error.toString() }}</span>
    </b-alert>

    <b-alert v-if="$page.flash.error" show variant="danger" dismissible>
      <h4 class="alert-heading">{{ $page.translation.error }}</h4>
      <span>{{ $page.flash.error }}</span>
    </b-alert>

    <b-alert v-if="$page.flash.success" show variant="success" dismissible>
      <h4 class="alert-heading">{{ $page.translation.success }}</h4>
      <span>{{ $page.flash.success }}</span>
    </b-alert>

    <b-alert v-if="$page.flash.info" show variant="info" dismissible>
      <h4 class="alert-heading">{{ $page.translation.info }}</h4>
      <span>{{ $page.flash.info }}</span>
    </b-alert>

    <b-alert v-if="$page.flash.warning" show variant="warning" dismissible>
      <h4 class="alert-heading">{{ $page.translation.warning }}</h4>
      <span>{{ $page.flash.warning }}</span>
    </b-alert>


  </div>
</template>

<script>
export default {
  name: "FlashMessages",
}
</script>
