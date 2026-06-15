import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
const OverviewController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OverviewController.url(options),
    method: 'get',
})

OverviewController.definition = {
    methods: ["get","head"],
    url: '/configuration/settings/scopes/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
OverviewController.url = (options?: RouteQueryOptions) => {
    return OverviewController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
OverviewController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OverviewController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/OverviewController.php:35
* @route '/configuration/settings/scopes/overview'
*/
OverviewController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: OverviewController.url(options),
    method: 'head',
})

export default OverviewController