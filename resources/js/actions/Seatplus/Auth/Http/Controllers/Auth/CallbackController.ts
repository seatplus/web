import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
const CallbackController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CallbackController.url(options),
    method: 'get',
})

CallbackController.definition = {
    methods: ["get","head"],
    url: '/auth/eve/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
CallbackController.url = (options?: RouteQueryOptions) => {
    return CallbackController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
CallbackController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CallbackController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\CallbackController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/CallbackController.php:20
* @route '/auth/eve/callback'
*/
CallbackController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CallbackController.url(options),
    method: 'head',
})

export default CallbackController