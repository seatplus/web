<template>
  <component
    :is="isInertiaButton ? 'Link': 'div'"
    as="button"
    :href="href"
    :method="method"
    :data="data"
    class="inline-flex items-center border border-gray-300 shadow-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    :class="{'px-2.5 py-1.5 text-xs font-medium' : buttonSize === 'xs', 'px-3 py-2 text-sm leading-4 font-medium' : buttonSize === 'small', 'px-4 py-2 text-sm font-medium' : buttonSize === 'medium', 'cursor-pointer' : !isInertiaButton}"
    @click="$emit('click')"
  >
    <slot />
  </component>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default {
    name: "Button",
    components: {Link},
    props: {
        buttonSize: {
            required: false,
            default: 'medium',
            type: String,
            validator: size => [
                'xs',
                'small',
                'medium',
                'large',
            ].includes(size)
        },
        href: {
            required: false,
            default: '',
            type: String
        },
        method: {
            required: false,
            default: 'get',
            type: String,
            validator: method => [
                'get',
                'post',
                'delete'
            ].includes(method)
        },
        data: {
            required: false,
            type: Object,
            default: () => new Object
        },
        isInertiaButton: {
            type: Boolean,
            required: false,
            default: true
        }
    },
    emits: ['click'],
}
</script>
