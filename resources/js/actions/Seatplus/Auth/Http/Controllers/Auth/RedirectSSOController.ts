import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
const RedirectSSOController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectSSOController.url(options),
    method: 'get',
})

RedirectSSOController.definition = {
    methods: ["get","head"],
    url: '/auth/eve/sso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
RedirectSSOController.url = (options?: RouteQueryOptions) => {
    return RedirectSSOController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
RedirectSSOController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectSSOController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
RedirectSSOController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: RedirectSSOController.url(options),
    method: 'head',
})

export default RedirectSSOController