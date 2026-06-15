import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
export const scopes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scopes.url(options),
    method: 'get',
})

scopes.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/global/view',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
scopes.url = (options?: RouteQueryOptions) => {
    return scopes.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
scopes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scopes.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
scopes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scopes.url(options),
    method: 'head',
})

const global = {
    scopes: Object.assign(scopes, scopes),
}

export default global