import { ref } from 'vue'
import { createGlobalState } from '@vueuse/core'

export const useGlobalState = createGlobalState(
    () => {
        const openScopeWarning = ref(true)

        function flipScopeWarning() {
            openScopeWarning.value = !openScopeWarning.value
        }

        return { openScopeWarning, flipScopeWarning }
    }
)