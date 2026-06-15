import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\PerformanceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/PerformanceController.php:12
* @route '/configuration/performance'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/configuration/performance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\PerformanceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/PerformanceController.php:12
* @route '/configuration/performance'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\PerformanceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/PerformanceController.php:12
* @route '/configuration/performance'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\PerformanceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/PerformanceController.php:12
* @route '/configuration/performance'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const performance = {
    index: Object.assign(index, index),
}

export default performance