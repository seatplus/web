import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
const LoginController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LoginController.url(options),
    method: 'get',
})

LoginController.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
LoginController.url = (options?: RouteQueryOptions) => {
    return LoginController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
LoginController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LoginController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
LoginController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LoginController.url(options),
    method: 'head',
})

export default LoginController