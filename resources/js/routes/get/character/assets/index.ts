import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
export const locations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: locations.url(options),
    method: 'get',
})

locations.definition = {
    methods: ["get","head"],
    url: '/character/assets/locations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
locations.url = (options?: RouteQueryOptions) => {
    return locations.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
locations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: locations.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
locations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: locations.url(options),
    method: 'head',
})

const assets = {
    locations: Object.assign(locations, locations),
}

export default assets