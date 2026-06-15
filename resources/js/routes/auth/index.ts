import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import eve5f058b from './eve'
/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
export const eve = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eve.url(options),
    method: 'get',
})

eve.definition = {
    methods: ["get","head"],
    url: '/auth/eve/sso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
eve.url = (options?: RouteQueryOptions) => {
    return eve.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
eve.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eve.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\RedirectSSOController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/RedirectSSOController.php:48
* @route '/auth/eve/sso'
*/
eve.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: eve.url(options),
    method: 'head',
})

const auth = {
    eve: Object.assign(eve, eve5f058b),
}

export default auth