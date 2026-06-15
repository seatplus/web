import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import corporation from './corporation'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
export const scopes = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scopes.url(options),
    method: 'post',
})

scopes.definition = {
    methods: ["post"],
    url: '/configuration/settings/scopes/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
scopes.url = (options?: RouteQueryOptions) => {
    return scopes.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
scopes.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scopes.url(options),
    method: 'post',
})

const create = {
    scopes: Object.assign(scopes, scopes),
    corporation: Object.assign(corporation, corporation),
}

export default create