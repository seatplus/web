import { onMounted, onUnmounted, ref } from "vue";

export function useInfinityScrolling(url, method = 'GET', postData = null) {

    const currentUrl = ref(url)
    const scrollComponent = ref(null)
    const result = ref([])
    const isLoading = ref(false)
    const isComplete = ref(false)
    const isVisible = ref(null)

    const source = axios.CancelToken.source()

    const fetchData = function () {

        if(isLoading.value || isComplete.value || _.isNil(currentUrl.value))
            return

        const timeout = setTimeout(() => isLoading.value = true, 250)

        axios({
            method: method,
            url: currentUrl.value,
            data: method === 'POST' ? postData : null,
            cancelToken: source.token,
        })
            .then(response => {

                clearTimeout(timeout)

                if (response.data.length === 0) {
                    isComplete.value = true;
                }

                result.value.push(...response.data.data);
                currentUrl.value = response.data.links.next;
            })
            .catch(error => {
                console.log(error);
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
