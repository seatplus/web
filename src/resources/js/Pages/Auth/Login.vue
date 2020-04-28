<template>
  <!--<div class="login-page" style="min-height: 512.8px;">
    <div class="login-box">
      <flash-messages/>
      <div class="login-logo">
        <b>SeAT</b>plus
      </div>
      &lt;!&ndash; /.login-logo &ndash;&gt;
      <div class="card">

        <div class="card-body login-card-body">
          {{ $page.login_welcome }}
          <div class="social-auth-links text-center mb-3">
            <a :href="route('auth.eve')">
                <b-img :src="$page.evesso_img_src" fluid />
            </a>
          </div>
        </div>
        &lt;!&ndash; /.login-card-body &ndash;&gt;
      </div>
    </div>
  </div>-->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div>
                <img class="mx-auto h-12 w-auto" src="/img/seat_plus_logo.svg" alt="Seat Plus" />
                <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                    SeAT plus
                </h2>
                <p class="mt-2 text-center text-sm leading-5 text-gray-600 max-w">
                    {{$I18n.trans('web::auth.login_welcome')}}
                </p>
            </div>

            <div class="mt-8">
                <a :href="$route('auth.eve')" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
          <span class="absolute left-0 inset-y pl-3">
            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition ease-in-out duration-150" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
          </span>
                    Sign in
                </a>
            </div>

            <!--<form class="mt-8" action="#" method="POST">
                <input type="hidden" name="remember" value="true" />
                <div class="rounded-md shadow-sm">
                    <div>
                        <input aria-label="Email address" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Email address" />
                    </div>
                    <div class="-mt-px">
                        <input aria-label="Password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Password" />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
                        <label for="remember_me" class="ml-2 block text-sm leading-5 text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm leading-5">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">
                            Forgot your password?
                        </a>
                    </div>
                </div>


            </form>-->
        </div>
    </div>
</template>

<script>

  export default {
    components: {},
    name: "Login",


  }
</script>

<style scoped>

</style>
