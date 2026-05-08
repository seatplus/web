import {ls} from './useLocalStorage'
import { prices as marketPrices } from '@/routes/get/markets'
import { apiFetch } from "@/Functions/apiFetch";

export function useGetPrice(type_id) {

    const prices = getPrices()

    function getPrices() {
        const prices = ls.get('markets.prices')

        if(prices)
            return prices

        apiFetch(marketPrices().url)
            .then(data => {
                ls.set('markets.prices', data, 86400000) // 24hrs
                return data
            })
    }

    return _.find(prices, {type_id: type_id})
}