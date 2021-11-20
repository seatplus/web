import {computed, onBeforeMount, onBeforeUnmount, ref} from "vue";
import route from 'ziggy'
import {useHydrateQueryParameters} from "./useHydrateQueryParameters";

export function useLoadCompleteResource(routeName, params, formData = {}) {

    const url = ref(route(routeName,useHydrateQueryParameters(params)))
    const results = ref([])

    const method = computed(() => _.isEmpty(formData) ? 'get' : 'post')
    const cleanFormData = computed(() => _.omitBy(formData, _.isNil))

    const CancelToken = axios.CancelToken;
    let cancel;

    const fetchData = async () => {

        let last_page = 1

        await axios.request({
            method: method.value,
            url: url.value,
            params: { page: 1 },
            data: cleanFormData.value
        })
            .then(response => {

                last_page = _.get(response, 'data.last_page', _.get(response, 'data.meta.last_page'))

                if (response.data.data.length) {
                    results.value.push(...response.data.data);
                }
            })
            .catch(error => console.log(error))

        const axiosrequests = []

        for(let i=2; i<= last_page; i++) {
            axiosrequests.push(axios.request({
                method: method.value,
                url: url.value,
                params: { page: i },
                data: cleanFormData.value
            }))
        }

        await axios.all(axiosrequests, {
            cancelToken: new CancelToken(function executor(c) {
                // An executor function receives a cancel function as a parameter
                cancel = c;
            })
        })
            .then(response => response.forEach(element => results.value.push(...element.data.data)))
            .catch(error => console.log(error))

    }

    onBeforeUnmount(() => {
        if (_.isFunction(cancel)) {
            cancel('Load complete resource request canceled.')
        }
    })


    onBeforeMount(async () => {
        await fetchData()
    })

    return {
        results,
    }
}
