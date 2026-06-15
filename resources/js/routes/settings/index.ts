import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::navigation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:40
* @route '/configuration/settings/navigation'
*/
export const navigation = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: navigation.url(options),
    method: 'get',
})

navigation.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/navigation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::navigation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:40
* @route '/configuration/settings/navigation'
*/
navigation.url = (options?: RouteQueryOptions) => {
    return navigation.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::navigation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:40
* @route '/configuration/settings/navigation'
*/
navigation.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: navigation.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::navigation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:40
* @route '/configuration/settings/navigation'
*/
navigation.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: navigation.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
export const scopes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scopes.url(options),
    method: 'get',
})

scopes.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
scopes.url = (options?: RouteQueryOptions) => {
    return scopes.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
scopes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scopes.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
scopes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scopes.url(options),
    method: 'head',
})

const settings = {
    navigation: Object.assign(navigation, navigation),
    scopes: Object.assign(scopes, scopes),
}

export default settings