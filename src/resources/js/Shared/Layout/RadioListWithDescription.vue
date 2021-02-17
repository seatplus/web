<template>
    <fieldset>
        <legend class="sr-only">
            {{ title }}
        </legend>

        <div class="bg-white rounded-md -space-y-px" ref="radiogroup">

            <div v-for="(option, index) in options" @click="select(index)" :class="[active === index ? 'bg-indigo-50 border-indigo-200 z-10' : 'border-gray-200', { 'rounded-tl-md rounded-tr-md': index === 0, 'rounded-bl-md rounded-br-md': index === options.length-1}]" class="relative border p-4 flex cursor-pointer">
                <div class="flex items-center h-5">
                    <input :id="title +'-settings-option-' + index" name="privacy_setting" type="radio"  @keydown.space="select(index)" @keydown.arrow-up="onArrowUp(index)" @keydown.arrow-down="onArrowDown(index)" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out cursor-pointer" :checked="active === index"/>
                </div>
                <label :for="title +'-settings-option-' + index" class="ml-3 flex flex-col cursor-pointer">
                    <span :class="{ 'text-indigo-900': active === index, 'text-gray-900': !(active === index) }" class="block text-sm leading-5 font-medium capitalize">
                        {{ option.title }}
                    </span>
                    <span :class="{ 'text-indigo-700': active === index, 'text-gray-500': !(active === index) }" class="block text-sm leading-5">
                        {{ option.description }}
                    </span>
                </label>
            </div>


            <!--   <div :class="{ 'border-gray-200': !(active === 1), 'bg-indigo-50 border-indigo-200 z-10': active === 1 }" class="relative border border-gray-200 p-4 flex">
                   <div class="flex items-center h-5">
                       <input id="settings-option-1" name="privacy_setting" type="radio" @click="select(1)" @keydown.space="select(1)" @keydown.arrow-up="onArrowUp(1)" @keydown.arrow-down="onArrowDown(1)" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out cursor-pointer" />
                   </div>
                   <label for="settings-option-1" class="ml-3 flex flex-col cursor-pointer">
                               <span :class="{ 'text-indigo-900': active === 1, 'text-gray-900': !(active === 1) }" class="block text-sm leading-5 font-medium">
                                   Private to Project Members
                               </span>
                       <span :class="{ 'text-indigo-700': active === 1, 'text-gray-500': !(active === 1) }" class="block text-sm leading-5">
                                   Only members of this project would be able to access
                               </span>
                   </label>
               </div>


               <div :class="{ 'border-gray-200': !(active === 2), 'bg-indigo-50 border-indigo-200 z-10': active === 2 }" class="relative border border-gray-200 rounded-bl-md rounded-br-md p-4 flex">
                   <div class="flex items-center h-5">
                       <input id="settings-option-2" name="privacy_setting" type="radio" @click="select(2)" @keydown.space="select(2)" @keydown.arrow-up="onArrowUp(2)" @keydown.arrow-down="onArrowDown(2)" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out cursor-pointer" />
                   </div>
                   <label for="settings-option-2" class="ml-3 flex flex-col cursor-pointer">
                               <span :class="{ 'text-indigo-900': active === 2, 'text-gray-900': !(active === 2) }" class="block text-sm leading-5 font-medium">
                                   Private to you
                               </span>
                       <span :class="{ 'text-indigo-700': active === 2, 'text-gray-500': !(active === 2) }" class="block text-sm leading-5">
                                   You are the only one able to access this project
                               </span>
                   </label>
               </div>-->

        </div>
    </fieldset>
</template>

<script>
export default {
    name: "RadioListWithDescription",
    props: {
        title: {
            default: () => 'Default'
        },
        options: {
            default: () => [
                {title: 'Title', description: 'description'},
                {title: 'Title1', description: 'description'},
                {title: 'Title2', description: 'description'}
            ]
        },
        value: {}
    },
    data() {
        return {
            active: this.value
        }
    },
    methods: {
        select(num1) {
            this.active = num1
        },
        onArrowUp(num1) {
            this.active = num1--
        },
        onArrowDown(num1) {
            this.active = num1++
        }
    },
    watch: {
        active(newVal) {
            this.$emit('input', this.id)
        }
    },
    computed: {
        id() {
            return _.get(this.options[this.active], 'id', this.active)
        }
    }
}
</script>

<style scoped>

</style>
