<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <!--
      Custom select controls like this require a considerable amount of JS to implement from scratch. We're planning
      to build some low-level libraries to make this easier with popular frameworks like React, Vue, and even Alpine.js
      in the near future, but in the mean time we recommend these reference guides when building your implementation:

      https://www.w3.org/TR/wai-aria-practices/#Listbox
      https://www.w3.org/TR/wai-aria-practices/examples/listbox/listbox-collapsible.html
    -->
    <div>
        <label id="listbox" class="block text-sm font-medium text-gray-700">
            <slot>Label</slot>
        </label>
        <div ref="selectField" class="mt-1 relative">
            <button @click="toggle" type="button" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label" class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <span class="block truncate">
                    {{ selectedValueName }}
                </span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <!-- Heroicon name: selector -->
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </span>
            </button>

            <div v-if="open"  class="fixed inset-0 transition-opacity">
                <div @click="toggle" class="absolute inset-0 bg-transparent"></div>
            </div>

            <!--
              Select popover, show/hide based on select state.

              Entering: ""
                From: ""
                To: ""
              Leaving: "transition ease-in duration-100"
                From: "opacity-100"
                To: "opacity-0"
            -->

                <transition
                    enter-active-class="duration-150 ease-out"
                    enter-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="duration-100 ease-in"
                    leave-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <Portal to="layout">
                    <div v-show="open" ref="listboxCollabsible" class="absolute mt-1 w-full rounded-md bg-white shadow-lg">
                        <ul tabindex="-1" role="listbox" aria-labelledby="listbox-label" class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            <!--
                              Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.

                              Highlighted: "text-white bg-indigo-600", Not Highlighted: "text-gray-900"
                            -->
                            <li v-for="(option, index) in options" :key="option.value"
                                :id="'listbox-item-' + index"
                                @click="select(option)"
                                role="option"
                                class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-8 pr-4">
                                <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                                <span :class="[isSelected(option) ? 'font-semibold' : 'font-normal', 'block truncate']">
                                    {{ option.text }}
                                </span>

                                <!--
                                  Checkmark, only display for selected option.

                                  Highlighted: "text-white", Not Highlighted: "text-indigo-600"
                                -->
                                <span v-show="isSelected(option)" class="absolute inset-y-0 left-0 flex items-center pl-1.5">
                                    <!-- Heroicon name: check -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </li>

                        </ul>
                    </div>
                    </Portal>
                </transition>
        </div>
    </div>

</template>

<script>
import {createPopper} from '@popperjs/core'
import {Portal} from 'portal-vue'

export default {
    name: "SelectComponent",
    components: {Portal},
    props: {
        value: {
            required: true
        },
        options: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            open: false,
        }
    },
    methods: {
        toggle() {
            this.open = !this.open

            if(this.open)
                var popper = new createPopper(this.$refs.selectField, this.$refs.listboxCollabsible, {
                    placement: 'bottom-start',
                    modifiers: [
                        {
                            name: "sameWidth",
                            enabled: true,
                            phase: "beforeWrite",
                            requires: ["computeStyles"],
                            fn: ({state}) => {
                                state.styles.popper.width = `${state.rects.reference.width}px`;
                            },
                            effect: ({state}) => {
                                state.elements.popper.style.width = `${
                                    state.elements.reference.offsetWidth
                                }px`;
                            },
                        },
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 8],
                            },
                        },
                    ]
                })

        },
        select(obj1) {

            this.$emit('input', obj1.value)
        },
        isSelected(option) {
            return _.isEqual(option.value, this.value)
        },
        getSelectedName() {

            console.log('called name', _.find(this.options, {'value': this.value}))
            return _.get(_.find(this.options, {value: this.value}), 'name', 'Select your option')
        }
    },
    computed: {
        selectedValueName() {
            return _.get(_.find(this.options, {'value': this.value}),'text', 'Select your option')

             /*return _.get(, 'name', 'Select your option')*/
        }
    }
}
</script>

<style scoped>

</style>
