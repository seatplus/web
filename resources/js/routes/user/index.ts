import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
export const settings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
settings.url = (options?: RouteQueryOptions) => {
    return settings.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
settings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
settings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(options),
    method: 'head',
})

const user = {
    settings: Object.assign(settings, settings),
}

export default user