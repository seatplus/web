import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manuel_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
export const manuel_locations = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manuel_locations.url(options),
    method: 'post',
})

manuel_locations.definition = {
    methods: ["post"],
    url: '/configuration/manual_locations/suggestions',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manuel_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
manuel_locations.url = (options?: RouteQueryOptions) => {
    return manuel_locations.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manuel_locations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
manuel_locations.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manuel_locations.url(options),
    method: 'post',
})

const accept = {
    manuel_locations: Object.assign(manuel_locations, manuel_locations),
}

export default accept