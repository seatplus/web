import {onBeforeMount, onBeforeUnmount, ref} from "vue";

export function useLoadCompleteResource(url, formData = {}) {

    const requestUrl = ref(url)
    const results = ref([])
    const isComplete = ref(true)

    const method = _.isEmpty(formData) ? 'get' : 'post'
    const cleanFormData = _.omitBy(formData, _.isNil)

    const CancelToken = axios.CancelToken;
    let cancelTokens = [];

    const fetchData = async () => {

        let last_page = 1

        await axios.request({
            method: method,
            url: requestUrl.value,
            params: { page: 1 },
            data: cleanFormData
        })
            .then(response => {

                last_page = _.get(response, 'data.last_page', _.get(response, 'data.meta.last_page'))

                if (response.data.data.length) {
                    results.value.push(...response.data.data);
                }
            })
            .catch(error => console.log(error))

        const axiosRequests = []

        for(let i=2; i<= last_page; i++) {
            axiosRequests.push(axios.request({
                method: method,
                url: requestUrl.value,
                params: { page: i },
                data: cleanFormData,
                cancelToken: new CancelToken(function executor(c) {
                    // An executor function receives a cancel function as a parameter
                    cancelTokens.push(c)
                })
            }))
        }

        await Promise.all(axiosRequests)
            .then(response => response.forEach(element => results.value.push(...element.data.data)))
            .finally(() => isComplete.value = true)
            .catch(error => console.log(error))

    }

    onBeforeUnmount(() => {
        cancelTokens.forEach(cancel => {
            if (_.isFunction(cancel)) {
                cancel('Load complete resource request canceled.')
            }
        })
    })


    onBeforeMount(async () => {
        await fetchData()
    })

    return {
        results,
        isComplete
    }
}
