import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
const index3b31db710f6006d7be94f3b99710c647 = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index3b31db710f6006d7be94f3b99710c647.url(args, options),
    method: 'get',
})

index3b31db710f6006d7be94f3b99710c647.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/view/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
index3b31db710f6006d7be94f3b99710c647.url = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index3b31db710f6006d7be94f3b99710c647.definition.url
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
index3b31db710f6006d7be94f3b99710c647.get = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index3b31db710f6006d7be94f3b99710c647.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/view/{entity_id}'
*/
index3b31db710f6006d7be94f3b99710c647.head = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index3b31db710f6006d7be94f3b99710c647.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
const index5c3f2d4c742695d6189ee5b2c1773fd3 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index5c3f2d4c742695d6189ee5b2c1773fd3.url(options),
    method: 'get',
})

index5c3f2d4c742695d6189ee5b2c1773fd3.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/global/view',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
index5c3f2d4c742695d6189ee5b2c1773fd3.url = (options?: RouteQueryOptions) => {
    return index5c3f2d4c742695d6189ee5b2c1773fd3.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
index5c3f2d4c742695d6189ee5b2c1773fd3.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index5c3f2d4c742695d6189ee5b2c1773fd3.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/global/view'
*/
index5c3f2d4c742695d6189ee5b2c1773fd3.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index5c3f2d4c742695d6189ee5b2c1773fd3.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/create'
*/
const index9a87c9c8f4e960217929378f9a56077c = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index9a87c9c8f4e960217929378f9a56077c.url(options),
    method: 'get',
})

index9a87c9c8f4e960217929378f9a56077c.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/create'
*/
index9a87c9c8f4e960217929378f9a56077c.url = (options?: RouteQueryOptions) => {
    return index9a87c9c8f4e960217929378f9a56077c.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/create'
*/
index9a87c9c8f4e960217929378f9a56077c.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index9a87c9c8f4e960217929378f9a56077c.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:53
* @route '/configuration/settings/scopes/create'
*/
index9a87c9c8f4e960217929378f9a56077c.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index9a87c9c8f4e960217929378f9a56077c.url(options),
    method: 'head',
})

export const index = {
    '/configuration/settings/scopes/view/{entity_id}': index3b31db710f6006d7be94f3b99710c647,
    '/configuration/settings/scopes/global/view': index5c3f2d4c742695d6189ee5b2c1773fd3,
    '/configuration/settings/scopes/create': index9a87c9c8f4e960217929378f9a56077c,
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/configuration/settings/scopes/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:68
* @route '/configuration/settings/scopes/create'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::deleteSsoScopeSetting
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
export const deleteSsoScopeSetting = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteSsoScopeSetting.url(args, options),
    method: 'delete',
})

deleteSsoScopeSetting.definition = {
    methods: ["delete"],
    url: '/configuration/settings/scopes/delete/{entity_id?}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::deleteSsoScopeSetting
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
deleteSsoScopeSetting.url = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entity_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            entity_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "entity_id",
    ])

    const parsedArgs = {
        entity_id: args?.entity_id,
    }

    return deleteSsoScopeSetting.definition.url
            .replace('{entity_id?}', parsedArgs.entity_id?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::deleteSsoScopeSetting
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
deleteSsoScopeSetting.delete = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteSsoScopeSetting.url(args, options),
    method: 'delete',
})

const SsoSettingsController = { index, create, deleteSsoScopeSetting }

export default SsoSettingsController