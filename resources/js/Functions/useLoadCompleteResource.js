import {onBeforeMount, onBeforeUnmount, ref} from "vue";
import { apiFetch } from "@/Functions/apiFetch";

export function useLoadCompleteResource(url, formData = {}) {

    const requestUrl = ref(url)
    const results = ref([])
    const isComplete = ref(true)

    const method = _.isEmpty(formData) ? 'GET' : 'POST'
    const cleanFormData = _.omitBy(formData, _.isNil)

    let abortController = null

    const fetchData = async () => {

        abortController = new AbortController()
        const signal = abortController.signal

        let last_page = 1

        try {
            const response = await apiFetch(requestUrl.value, {
                method,
                params: { page: 1 },
                data: cleanFormData,
                signal,
            })

            last_page = _.get(response, 'last_page', _.get(response, 'meta.last_page', 1))

            if (response.data.length) {
                results.value.push(...response.data)
            }
        } catch (error) {
            if (error.name !== 'AbortError') console.log(error)
            return
        }

        const requests = []

        for (let i = 2; i <= last_page; i++) {
            requests.push(
                apiFetch(requestUrl.value, {
                    method,
                    params: { page: i },
                    data: cleanFormData,
                    signal,
                }).then(response => results.value.push(...response.data))
            )
        }

        await Promise.all(requests)
            .catch(error => {
                if (error.name !== 'AbortError') console.log(error)
            })
            .finally(() => isComplete.value = true)
    }

    onBeforeUnmount(() => {
        if (abortController) abortController.abort()
    })

    onBeforeMount(async () => {
        await fetchData()
    })

    return {
        results,
        isComplete
    }
}
