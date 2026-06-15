import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
export const settings = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(args, options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/view/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
settings.url = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entity_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            entity_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        entity_id: args.entity_id,
    }

    return settings.definition.url
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
settings.get = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::settings
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
settings.head = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(args, options),
    method: 'head',
})

const scopes = {
    settings: Object.assign(settings, settings),
}

export default scopes