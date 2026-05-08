import {ls} from './useLocalStorage'
import axios from "axios";
import { prices as marketPrices } from '@/routes/get/markets'

export function useGetPrice(type_id) {

    const prices = getPrices()

    function getPrices() {
        const prices = ls.get('markets.prices')

        if(prices)
            return prices

        axios.get(marketPrices().url)
            .then(response => {
                ls.set('markets.prices', response.data, 86400000) // 24hrs
                return response.data
            })
    }

    return _.find(prices, {type_id: type_id})
}