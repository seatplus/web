<template>
  <button
    ref="menu"
    class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150"
    @click="toggleMenu"
  >
    <svg
      class="w-5 h-5 text-gray-400"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
      <path
        fill-rule="evenodd"
        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
        clip-rule="evenodd"
      />
    </svg>
    <span class="ml-3">Apply</span>
  </button>
  <!--<div>
            <button @click="toggleMenu" class="max-w-xs flex items-center text-sm rounded-full focus:outline-none focus:ring">
                <EveImage tailwind_class="h-8 w-8 rounded-full" :object="$page.user.data.main_character"  test :size="256" />
            </button>
        </div>-->
  <div
    v-if="menuOpen"
    class="fixed inset-0 transition-opacity"
  >
    <div
      class="absolute inset-0 bg-transparent"
      @click="toggleMenu"
    />
  </div>
  <transition
    enter-active-class="duration-150 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="duration-100 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-show="menuOpen"
      ref="list"
      class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg"
    >
      <div class="py-1 rounded-md bg-white ring-1 ring-black ring-opacity-5">
        <inertia-link
          v-for="enlistment in enlistments"
          :key="enlistment.corporation_id"
          method="post"
          :href="$route('post.application')"
          :data="{corporation_id: enlistment.corporation_id, character_id: character_id}"
          :class="'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150 text-left'"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <EveImage
                tailwind_class="h-8 w-8 rounded-full"
                :size="256"
                :object="enlistment.corporation"
              />
            </div>
            <div class="ml-4">
              <h3 class="text-sm leading-6 font-medium text-gray-900">
                {{ enlistment.corporation.name }}
              </h3>
              <p class="text-xs leading-5 text-gray-500">
                {{ enlistment.corporation.alliance ? enlistment.corporation.alliance.name : '' }}
              </p>
            </div>
          </div>
        </inertia-link>
      </div>
    </div>
  </transition>
</template>

<script>
import EveImage from "@/Shared/EveImage"
import {createPopper} from '@popperjs/core'
// TODO: Fragment
//import { Fragment } from 'vue-fragment'

export default {
    name: "CharacterApplication",
    components: {EveImage,
        //Fragment
    },
    props: {
        enlistments: {
            type: Array
        },
        character_id: {
            type: Number
        }
    },
    data() {
        return {
            menuOpen: false
        }
    },
    created() {

    },
    methods: {
        toggleMenu() {
            this.menuOpen = ! this.menuOpen;

            if(this.menuOpen)
                createPopper(this.$refs.menu, this.$refs.list, {
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, 8],
                        },
                    },
                ]
            });
        },
    }
}
</script>

<style scoped>

</style>
