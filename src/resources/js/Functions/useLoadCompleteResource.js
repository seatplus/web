import { onBeforeMount, ref} from "vue";
import route from 'ziggy'
import {useHydrateQueryParameters} from "./useHydrateQueryParameters";

export function useLoadCompleteResource(routeName, params) {

    const url = ref(route(routeName,useHydrateQueryParameters(params)))
    const results = ref([])
    const page = ref(1)

    const isInitialRequestLoading = ref(true)
    const isLoading = ref(false)
    const isComplete = ref(false)

    const fetchData = async () => {

        if(isLoading.value || isComplete.value)
            return

        isLoading.value = true
        let last_page = 1

        await axios.get(url.value, {
            params: {
                page: page.value,
            },
        })
            .then(response => {

                last_page = response.data.last_page

                if (response.data.data.length) {
                    results.value.push(...response.data.data);
                }
            })
            .catch(error => console.log(error))
            .finally(isInitialRequestLoading.value = false)


        const axiosrequests = []

        for(let i=2; i<= last_page; i++) {
            axiosrequests.push(axios.get(url.value, {params: {page: i}}))
        }

        await axios.all(axiosrequests).then(response => response.forEach(element => results.value.push(...element.data.data)))
            .finally(() => {
                isLoading.value = false
                isComplete.value = true
            })

    }

    onBeforeMount(async () => {
        await fetchData()
        isInitialRequestLoading.value = false
    })

    return {
        results,
        isLoading,
        isComplete,
        isInitialRequestLoading
    }
}
