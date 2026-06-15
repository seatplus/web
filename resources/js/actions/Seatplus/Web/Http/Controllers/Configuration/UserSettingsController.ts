import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\UserSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/UserSettingsController.php:35
* @route '/configuration/settings/user'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const UserSettingsController = { index }

export default UserSettingsController