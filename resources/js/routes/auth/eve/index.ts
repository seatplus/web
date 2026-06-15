import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
export const step_up = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: step_up.url(args, options),
    method: 'get',
})

step_up.definition = {
    methods: ["get","head"],
    url: '/auth/eve/sso/{character_id}/step_up',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
step_up.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return step_up.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
step_up.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: step_up.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
step_up.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: step_up.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
export const callback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})

callback.definition = {
    methods: ["get","head"],
    url: '/auth/eve/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
callback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
callback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: callback.url(options),
    method: 'head',
})

const eve = {
    step_up: Object.assign(step_up, step_up),
    callback: Object.assign(callback, callback),
}

export default eve