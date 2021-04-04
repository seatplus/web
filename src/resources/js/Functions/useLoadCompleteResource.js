import {onBeforeMount, ref} from "vue";
import route from 'ziggy'

export function useLoadCompleteResource(routeName, params) {

    const url = ref(route(routeName,params))
    const results = ref([])
    const page = ref(1)

    const isInitialRequestLoading = ref(true)
    const isLoading = ref(false)
    const isComplete = ref(false)

    const fetchData = async () => {

        if(isLoading.value || isComplete.value)
            return

        isLoading.value = true

        await axios.get(url.value, {
            params: {
                page: page.value,
            },
        })
            .then(response => {
                if (response.data.data.length) {
                    page.value += 1;
                    results.value.push(...response.data.data);
                    isLoading.value = false
                    fetchData()
                }
                else {
                    isComplete.value = true
                }
            })
            .catch(error => console.log(error))
            //.finally(await fetchData());
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
