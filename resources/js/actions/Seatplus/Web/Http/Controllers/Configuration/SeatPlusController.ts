import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
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

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::impersonate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
export const impersonate = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: impersonate.url(args, options),
    method: 'get',
})

impersonate.definition = {
    methods: ["get","head"],
    url: '/configuration/start/impersonate/{user_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::impersonate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
impersonate.url = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            user_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user_id: args.user_id,
    }

    return impersonate.definition.url
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::impersonate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
impersonate.get = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: impersonate.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::impersonate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
impersonate.head = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: impersonate.url(args, options),
    method: 'head',
})

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

const SeatPlusController = { settings, impersonate, navigation }

export default SeatPlusController