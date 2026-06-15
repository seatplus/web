import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
export const affiliatable = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: affiliatable.url(options),
    method: 'get',
})

affiliatable.definition = {
    methods: ["get","head"],
    url: '/acl/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
affiliatable.url = (options?: RouteQueryOptions) => {
    return affiliatable.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
affiliatable.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: affiliatable.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/SearchAffiliatableController.php:17
* @route '/acl/search'
*/
affiliatable.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: affiliatable.url(options),
    method: 'head',
})

const search = {
    affiliatable: Object.assign(affiliatable, affiliatable),
}

export default search