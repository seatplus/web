<template>
  <div class="flex items-center flex-shrink-0">
    <div class="flex-shrink-0">
      <span class="inline-block relative">
        <EveImage
          :object="character"
          :size="256"
          tailwind_class="`h-8 w-8 rounded-full"
        />
        <!--        <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-400" />-->
        <div
          class="absolute bottom-0 right-0 flex items-center justify-center h-3 w-3 ring-2 ring-white rounded-full"
          :class="hasMissing > 0 ? 'bg-red-400' : 'bg-green-400'"
        >
          <XIcon
            v-if="hasMissing > 0"
            class="h-2 w-2 text-red-900"
          />
          <CheckIcon
            v-else
            class="h-2 w-2 text-green-900"
          />

        </div>
      </span>
    </div>
    <div class="ml-4">
      <h3 class="text-md leading-6 font-medium text-gray-900">
        {{ character.name }}
      </h3>
    </div>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage";
import { XIcon, CheckIcon } from '@heroicons/vue/outline'
import {computed} from "vue";
export default {
    name: "CharacterComplianceElement",
    components: {EveImage, XIcon, CheckIcon },
    props: {
        character: {
            required: true,
            type: Object
        }
    },
    setup(props) {
        const hasMissing = computed(() => {
            let missingScopes = _.isObject(props.character.missing_scopes) ? Object.values(props.character.missing_scopes) : props.character.missing_scopes

            return missingScopes.length > 0
        })

        return {
            hasMissing
        }
    }

}
</script>

<style scoped>

</style>
