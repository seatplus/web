<template>
  <div
    v-show="enlistments"
    class="pb-5 border-b border-gray-200 space-y-2"
  >
    <h3 class="text-lg leading-6 font-medium text-gray-900">
      Job Postings
    </h3>
    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      The following corporations are open for new recruits. These job listings do have two kind of characteristics: either 'character' or 'user' and it's defined by  a senior recruiter of that corporation.
      For an enlistment with character type, this means you are able to apply with single characters of yours and sso-scope requirements are only enforced per applied character.
      If an enlistment is of user type, upon application each and every character that belongs to your user account must fulfill the set sso-scope requirements of the recruiting corporation.
    </p>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <Enlistment
        v-for="enlistment in enlistments"
        :key="enlistment.corporation_id"
        :enlistment="enlistment"
      />
    </div>
  </div>
</template>

<script>
import {useLoadCompleteResource} from "../../Functions/useLoadCompleteResource";
import {computed} from "vue";
import Enlistment from "./Enlistment";
export default {
    name: "Enlistments",
    components: {Enlistment},
    setup() {
        const completeResource = useLoadCompleteResource('list.open.enlistments')

        const enlistments = computed(() => completeResource.results.value)

        return {
            enlistments
        }
    },
}
</script>

<style scoped>

</style>
