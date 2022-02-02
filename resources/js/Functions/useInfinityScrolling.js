import { onMounted, onUnmounted, ref } from "vue";
import route from 'ziggy'

export function useInfinityScrolling(routeName, params) {

    const url = route(routeName,params)
    const scrollComponent = ref(null)
    const result = ref([])
    const page = ref(1)
    const isLoading = ref(false)
    const isComplete = ref(false)
    const isVisible = ref(null)

    const source = axios.CancelToken.source()

    const fetchData = function () {

        if(isLoading.value || isComplete.value)
            return

        const timeout = setTimeout(() => isLoading.value = true, 250)

        axios.get(url, {
            cancelToken: source.token,
            params: {
                page: page.value,
            },
        }).then(response => {

            clearTimeout(timeout)
            isLoading.value = false

            if (response.data.data.length) {
                page.value += 1;
                result.value.push(...response.data.data);

                // Todo check if still in view and fetch again

            } else {
                isComplete.value = true
            }
        }).catch(error => console.log(error));
    }

    const options =  {
        threshold: [1]
    }

    function handleIntersect(entries) {
        if(entries[0].isIntersecting === true) {

            isVisible.value = true

            if(isComplete.value || isLoading.value)
                return

            fetchData()
        } else {

            isVisible.value = false
        }
    }

    const observer = new IntersectionObserver(handleIntersect, options)

    onMounted(() => observer.observe(scrollComponent.value))

    onUnmounted(() => {
        observer.disconnect()
        source.cancel()
    })

    return {
        scrollComponent,
        result,
        isComplete,
        isLoading,
        isVisible
    }
}
