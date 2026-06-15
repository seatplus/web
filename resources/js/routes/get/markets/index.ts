import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::prices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
export const prices = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: prices.url(options),
    method: 'get',
})

prices.definition = {
    methods: ["get","head"],
    url: '/shared/markets/prices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::prices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
prices.url = (options?: RouteQueryOptions) => {
    return prices.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::prices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
prices.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: prices.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::prices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
prices.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: prices.url(options),
    method: 'head',
})

const markets = {
    prices: Object.assign(prices, prices),
}

export default markets