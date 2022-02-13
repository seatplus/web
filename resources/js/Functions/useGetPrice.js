import route from 'ziggy'
import {ls} from './useLocalStorage'
import axios from "axios";

export function useGetPrice(type_id) {

    const prices = getPrices()

    function getPrices() {
        const prices = ls.get('markets.prices')

        if(prices)
            return prices

        axios.get(route('get.markets.prices'))
            .then(response => {
                ls.set('markets.prices', response.data, 86400000) // 24hrs
                return response.data
            })
    }

    return _.find(prices, {type_id: type_id})
}