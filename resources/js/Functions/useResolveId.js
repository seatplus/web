import {ref} from "vue";
import { id as resolveId } from '@/routes/resolve'
import { apiFetch } from "@/Functions/apiFetch";

export function useResolveId(id) {

    let result = ref()

    apiFetch(resolveId(id).url)
        .then((data) => result.value = data)
        .catch(error => console.log(error))

    return result
}