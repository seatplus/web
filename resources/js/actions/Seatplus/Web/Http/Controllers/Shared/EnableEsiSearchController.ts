import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
const EnableEsiSearchController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EnableEsiSearchController.url(options),
    method: 'get',
})

EnableEsiSearchController.definition = {
    methods: ["get","head"],
    url: '/shared/esi-search/enable_esi_search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
EnableEsiSearchController.url = (options?: RouteQueryOptions) => {
    return EnableEsiSearchController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
EnableEsiSearchController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EnableEsiSearchController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
EnableEsiSearchController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EnableEsiSearchController.url(options),
    method: 'head',
})

export default EnableEsiSearchController