import { onMounted, onUnmounted, ref } from "vue";
import { apiFetch } from "@/Functions/apiFetch";

export function useInfinityScrolling(url, method = 'GET', postData = null) {

    const currentUrl = ref(url)
    const scrollComponent = ref(null)
    const result = ref([])
    const isLoading = ref(false)
    const isComplete = ref(false)
    const isVisible = ref(null)

    let abortController = null

    const fetchData = function () {

        if(isLoading.value || isComplete.value || _.isNil(currentUrl.value))
            return

        const timeout = setTimeout(() => isLoading.value = true, 250)

        abortController = new AbortController()

        apiFetch(currentUrl.value, {
            method,
            data: method === 'POST' ? postData : null,
            signal: abortController.signal,
        })
            .then(response => {

                clearTimeout(timeout)

                if (response.data.length === 0) {
                    isComplete.value = true;
                }

                result.value.push(...response.data);
                currentUrl.value = response.links.next;
            })
            .catch(error => {
                if (error.name !== 'AbortError') console.log(error)
            }).finally(() => {
                isLoading.value = false;
            });
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
        if (abortController) abortController.abort()
    })

    return {
        scrollComponent,
        result,
        isComplete,
        isLoading,
        isVisible
    }
}
