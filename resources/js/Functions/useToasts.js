import {computed, nextTick, ref, watch} from 'vue';
import { v4 as uuidv4 } from 'uuid';
import {filter, uniqBy} from "lodash";
import { sharedToasts } from "@/Functions/sharedToasts.js";

export function useToasts(){

    //const toasts = ref([]);
    const types = ['info', 'success', 'error', 'warning'];

    const visibleToasts = computed(() => {
        return uniqBy(sharedToasts.value.slice(0, 4), 'id');
    });

    const addToast = (message, options = {}) => {

        const {
            appearance = options.appearance ?? types[0],
            autoDismiss = options.autoDismiss ?? true, // default to true, unless set to false
        } = options;

        validateAppearance(appearance)

        // create a unique id for each toast
        const id = uuidv4();

        // If autoDismiss is true, remove the toast after dismissTime
        if (autoDismiss && appearance !== 'error') {
            scheduleRemoval(id)
        }

        // add the toast to the beginning of the array
        sharedToasts.value.unshift({
            id,
            appearance,
            message,
        });

    }

    const removeToast = (id) => {
        sharedToasts.value = filter(sharedToasts.value, (toast) => toast.id !== id);
    }

    const scheduleRemoval = (id) => {
        setTimeout(() => {
            removeToast(id);
        }, 10_000);
    }

    const validateAppearance = (appearance) => {
        if (!types.includes(appearance)) {
            throw new Error(`Appearance must be one of the following: ${types.join(', ')}`);
        }
    }

    return {
        addToast,
        visibleToasts,
        types,
        removeToast
    };
}