import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::start
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
export const start = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})

start.definition = {
    methods: ["get","head"],
    url: '/configuration/start/impersonate/{user_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::start
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
start.url = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return start.definition.url
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::start
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
start.get = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SeatPlusController::start
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SeatPlusController.php:69
* @route '/configuration/start/impersonate/{user_id}'
*/
start.head = (args: { user_id: string | number } | [user_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: start.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
export const recruit = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruit.url(args, options),
    method: 'get',
})

recruit.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/impersonate/{application_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
recruit.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return recruit.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
recruit.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruit.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
recruit.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recruit.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
export const stop = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stop.url(options),
    method: 'get',
})

stop.definition = {
    methods: ["get","head"],
    url: '/stop/impersonate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
stop.url = (options?: RouteQueryOptions) => {
    return stop.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
stop.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stop.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
stop.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stop.url(options),
    method: 'head',
})

const impersonate = {
    start: Object.assign(start, start),
    recruit: Object.assign(recruit, recruit),
    stop: Object.assign(stop, stop),
}

export default impersonate