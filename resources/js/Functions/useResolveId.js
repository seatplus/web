import axios from "axios";
import {ref} from "vue";

export function useResolveId(id) {

    let result = ref()

    axios.get(route('resolve.id', id))
        .then((response) => result.value = response.data)
        .catch(error => console.log(error))

    return result
}