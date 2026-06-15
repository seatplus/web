import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:47
* @route '/configuration/settings'
*/
export const settings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/configuration/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:47
* @route '/configuration/settings'
*/
settings.url = (options?: RouteQueryOptions) => {
    return settings.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:47
* @route '/configuration/settings'
*/
settings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:47
* @route '/configuration/settings'
*/
settings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(options),
    method: 'head',
})

const server = {
    settings: Object.assign(settings, settings),
}

export default server