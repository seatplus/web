<template>
  <div class="login-page" style="min-height: 512.8px;">
    <div class="login-box">
      <flash-messages/>
      <div class="login-logo">
        <b>SeAT</b>plus
      </div>
      <!-- /.login-logo -->
      <div class="card">

        <div class="card-body login-card-body">
          {{ $page.login_welcome }}
          <div class="social-auth-links text-center mb-3">
            <inertia-link :href="route('auth.eve')">
                <b-img :src="$page.evesso_img_src" fluid />
            </inertia-link>
          </div>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
  </div>
</template>

<script>
  import FlashMessages from '@/Shared/FlashMessages'

  export default {
    components: {
      FlashMessages,
    },
    name      : "Login"


  }
</script>

<style scoped>

</style>
