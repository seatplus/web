import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
const LogoutController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LogoutController.url(options),
    method: 'post',
})

LogoutController.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
LogoutController.url = (options?: RouteQueryOptions) => {
    return LogoutController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
LogoutController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LogoutController.url(options),
    method: 'post',
})

export default LogoutController