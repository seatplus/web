import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import acl from './acl'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
export const manual_locations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manual_locations.url(options),
    method: 'get',
})

manual_locations.definition = {
    methods: ["get","head"],
    url: '/configuration/manual_locations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
manual_locations.url = (options?: RouteQueryOptions) => {
    return manual_locations.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
manual_locations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manual_locations.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
manual_locations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manual_locations.url(options),
    method: 'head',
})

const manage = {
    manual_locations: Object.assign(manual_locations, manual_locations),
    acl: Object.assign(acl, acl),
}

export default manage