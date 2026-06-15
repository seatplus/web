import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::suggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
export const suggestions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggestions.url(options),
    method: 'get',
})

suggestions.definition = {
    methods: ["get","head"],
    url: '/configuration/manual_locations/suggestions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::suggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
suggestions.url = (options?: RouteQueryOptions) => {
    return suggestions.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::suggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
suggestions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggestions.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::suggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
suggestions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: suggestions.url(options),
    method: 'head',
})

const manuel_locations = {
    suggestions: Object.assign(suggestions, suggestions),
}

export default manuel_locations