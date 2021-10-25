import {onBeforeMount, onBeforeUnmount, ref} from "vue";
import route from 'ziggy'
import {useHydrateQueryParameters} from "./useHydrateQueryParameters";

export function useLoadCompleteResource(routeName, params) {

    const url = ref(route(routeName,useHydrateQueryParameters(params)))
    const results = ref([])

    const axiosCancelSource = ref(null);

    const fetchData = async () => {

        let last_page = 1

        await axios.get(url.value, { params: { page: 1 }})
            .then(response => {

                last_page = _.get(response, 'data.last_page', _.get(response, 'data.meta.last_page'))

                if (response.data.data.length) {
                    results.value.push(...response.data.data);
                }
            })
            .catch(error => console.log(error))

        const axiosrequests = []

        for(let i=2; i<= last_page; i++) {
            axiosrequests.push(axios.get(url.value, {params: {page: i}}))
        }

        axiosCancelSource.value = axios.CancelToken.source()

        await axios.all(axiosrequests, {
            cancelToken: axiosCancelSource.value.token
        })
            .then(response => response.forEach(element => results.value.push(...element.data.data)))
            .catch(error => console.log(error))

    }

    onBeforeUnmount(() => axiosCancelSource.value.cancel('Axios request canceled.'))

    onBeforeMount(async () => {
        await fetchData()
    })

    return {
        results,
    }
}
