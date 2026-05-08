import axios from "axios";
import {ref} from "vue";
import { id as resolveId } from '@/routes/resolve'

export function useResolveId(id) {

    let result = ref()

    axios.get(resolveId(id).url)
        .then((response) => result.value = response.data)
        .catch(error => console.log(error))

    return result
}