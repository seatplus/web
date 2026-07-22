import {ref} from "vue";
import { getJson } from "@/Functions/http";
import { getEntityFromId } from "@/actions/Seatplus/Web/Http/Controllers/Shared/HelperController";

export function useResolveId(id) {

    let result = ref()

    getJson(getEntityFromId.url(id))
        .then((data) => result.value = data)
        .catch(error => console.log(error))

    return result
}
