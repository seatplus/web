<template>
    <Portal to="layout">
        <Modal v-model="open">
            <!--Modal-->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <!-- Heroicon name: check -->
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Profile
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            This information will be displayed publicly so be careful what you share.
                        </p>
                        <!--Decision-->
                        <div class="mt-6 sm:mt-5 sm:border-t sm:border-gray-200 sm:pt-5">
                            <div role="group" aria-labelledby="label-notifications">
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                                    <div>
                                        <div class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700" id="label-notifications">
                                            Push Notifications
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <div class="max-w-lg">
                                            <p class="text-sm leading-5 text-gray-500">These are delivered via SMS to your mobile phone.</p>
                                            <div class="mt-4">
                                                <div class="flex items-center">
                                                    <input id="push_everything" name="push_notifications" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                    <label for="push_everything" class="ml-3">
                                                        <span class="block text-sm leading-5 font-medium text-gray-700">Everything</span>
                                                    </label>
                                                </div>
                                                <div class="mt-4 flex items-center">
                                                    <input id="push_email" name="push_notifications" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                    <label for="push_email" class="ml-3">
                                                        <span class="block text-sm leading-5 font-medium text-gray-700">Same as email</span>
                                                    </label>
                                                </div>
                                                <!--<div class="mt-4 flex items-center">
                                                    <input id="push_nothing" name="push_notifications" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                    <label for="push_nothing" class="ml-3">
                                                        <span class="block text-sm leading-5 font-medium text-gray-700">No push notifications</span>
                                                    </label>
                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Explanation-->
                        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="about" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                                About
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div class="max-w-lg flex rounded-md shadow-sm">
                                    <textarea id="about" rows="3" class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"></textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Write a few sentences about yourself.</p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
        <span class="flex w-full rounded-md shadow-sm">
          <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5">
            Go back to dashboard
          </button>
        </span>
                </div>
            </div>
            <!--Modal End-->
        </Modal>
    </Portal>
</template>

<script>
import Modal from "@/Shared/Modals/Modal";
import { Portal } from 'portal-vue'

export default {
    name: "UserApplicationModal",
    components: {Modal, Portal},
    props: {
        value: {}
    },
    data() {
        return {
            open: false
        }
    },
    watch: {
        value() {
            this.open = _.isObject(this.value)
        },
        open(newValue) {
            if (!newValue)
                this.$emit('input', null)
        }
    }
}
</script>

<style scoped>

</style>
