import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getEnlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
export const getEnlistments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEnlistments.url(options),
    method: 'get',
})

getEnlistments.definition = {
    methods: ["get","head"],
    url: '/enlistments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getEnlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
getEnlistments.url = (options?: RouteQueryOptions) => {
    return getEnlistments.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getEnlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
getEnlistments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEnlistments.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getEnlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
getEnlistments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEnlistments.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getOwnApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:54
* @route '/applications/{corporation_id}/related'
*/
export const getOwnApplications = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOwnApplications.url(args, options),
    method: 'get',
})

getOwnApplications.definition = {
    methods: ["get","head"],
    url: '/applications/{corporation_id}/related',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getOwnApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:54
* @route '/applications/{corporation_id}/related'
*/
getOwnApplications.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { corporation_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
    }

    return getOwnApplications.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getOwnApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:54
* @route '/applications/{corporation_id}/related'
*/
getOwnApplications.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOwnApplications.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::getOwnApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:54
* @route '/applications/{corporation_id}/related'
*/
getOwnApplications.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getOwnApplications.url(args, options),
    method: 'head',
})

const HomeController = { home, getEnlistments, getOwnApplications }

export default HomeController