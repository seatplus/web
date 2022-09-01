<template>
  <SwitchGroup
    as="div"
    class="flex items-center"
  >
    <Switch
      v-model="enabled"
      :class="[enabled ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']"
    >
      <span class="sr-only">Use setting</span>
      <span
        aria-hidden="true"
        :class="[enabled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
      />
    </Switch>
    <SwitchLabel
      as="span"
      class="ml-3"
    >
      <span class="text-sm font-medium text-gray-900">{{ scope.text }}</span>
      <!-- <span class="text-sm text-gray-500">(Save 10%)</span>-->
    </SwitchLabel>
  </SwitchGroup>
</template>

<script>
import { ref, watch} from 'vue'
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

export default {
    name: "ScopeToggle",
    components: {
        Switch,
        SwitchGroup,
        SwitchLabel,
    },
    props: {
        scope: {
            type: Object,
            required: true
        },
        selected: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    emits: ['update:selected'],
    setup(props, context) {

        const intersection = _.intersection(props.selected, props.scope.value)

        const enabled = ref(_.isEqual(props.scope.value.sort(), intersection.sort()))
        const selected = ref(props.selected)

        watch(enabled,() => {

            if(enabled.value) {

                context.emit('update:selected', _.uniq([...selected.value, ...props.scope.value]))

            } else {

                // 1. remove scope props
                selected.value = selected.value.filter( (el) => !props.scope.value.includes(el))

                context.emit('update:selected', selected.value)
            }
        })

        return {
            enabled,
        }
    },
    methods: {
        test() {
            console.log('test')
        }
    }
}
</script>

<style scoped>

</style>