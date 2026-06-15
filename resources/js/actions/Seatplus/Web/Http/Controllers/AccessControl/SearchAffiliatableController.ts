import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
const SearchAffiliatableController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SearchAffiliatableController.url(options),
    method: 'get',
})

SearchAffiliatableController.definition = {
    methods: ["get","head"],
    url: '/acl/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
SearchAffiliatableController.url = (options?: RouteQueryOptions) => {
    return SearchAffiliatableController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
SearchAffiliatableController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SearchAffiliatableController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
SearchAffiliatableController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SearchAffiliatableController.url(options),
    method: 'head',
})

export default SearchAffiliatableController