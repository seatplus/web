<template>
  <div class="space-y-3">
    <PageHeader :breadcrumbs="breadcrumbs">
      Access Control Groups
      <template #primary>
        <HeaderButton
          :secondary="true"
          @click="remove"
        >
          Delete
        </HeaderButton>
        <HeaderButton @click="store">
          Save
        </HeaderButton>
      </template>
    </PageHeader>

    <div class="bg-white overflow-hidden shadow rounded-lg mb-3">
      <div class="px-4 py-5 sm:p-6">
        <div>
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Settings
            </h3>
            <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
              First set a name and which permission should be applied to the access control group
            </p>
          </div>
          <div class="mt-6 sm:mt-5">
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
              <label
                for="roleName"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Access control group name
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input
                    id="roleName"
                    v-model="form.roleName"
                    :placeholder="form.roleName"
                    type="text"
                    class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                  >
                </div>
              </div>
            </div>

            <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
              <label
                for="permissions"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Permissions
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div
                  id="permissions"
                  class="sm:grid sm:grid-cols-2 sm:gap-4"
                >
                  <div
                    v-for="permission in availablePermissions"
                    :key="permission"
                  >
                    <label class="inline-flex items-center">
                      <input
                        v-model="form.permissions"
                        type="checkbox"
                        :value="permission"
                        class="form-checkbox"
                      >
                      <span class="ml-2">{{ permission }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <EditSettings v-model:affiliations="selectedAffiliations" />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader"
import HeaderButton from "@/Shared/Layout/HeaderButton"
import Layout from "@/Shared/SidebarLayout/Layout";
import EditSettings from "./Edit/EditSettings";
import {useForm} from "@inertiajs/inertia-vue3/src";
import {ref} from "vue";
import route from 'ziggy'
export default {
    name: "EditGroup",
    components: {
        EditSettings,
        HeaderButton,
        PageHeader
    },
    props: {
        role: {
            type: Object,
            required: true
        },
        affiliations: {
            type: Object,
            required: true
        },
        permissions: {
            type: Array,
            required: true
        },
        availablePermissions: {
            type: Array,
            required: true
        },
        activeSidebarElement: {
            type: String,
            required: true
        }
    },
    setup(props) {

        const selectedAffiliations = ref(props.affiliations)


        const form = useForm({
            roleName: props.role.name,
            permissions: _.map(props.permissions, (permission) => permission.name)
        })

        const store = function () {

            return form.transform((data) => ({
                    ...data,
                    affiliations: selectedAffiliations.value,
            })).post(route('acl.update', props.role.id))

        }

        const remove = function () {

            this.$inertia.delete(this.$route('acl.delete', this.role.id), {
                replace: false,
                preserveState: false,
                preserveScroll: false,
                only: [],
            })
        }

        return {
            form,
            selectedAffiliations,
            store,
            breadcrumbs: [
                {
                    name: 'Control Group',
                    route: route('acl.groups')
                }
            ],
            remove
        }
    },
}
</script>

<style scoped>

</style>
