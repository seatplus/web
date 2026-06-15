import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\HomeController::enlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
export const enlistments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enlistments.url(options),
    method: 'get',
})

enlistments.definition = {
    methods: ["get","head"],
    url: '/enlistments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::enlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
enlistments.url = (options?: RouteQueryOptions) => {
    return enlistments.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::enlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
enlistments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enlistments.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::enlistments
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:49
* @route '/enlistments'
*/
enlistments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enlistments.url(options),
    method: 'head',
})

const open = {
    enlistments: Object.assign(enlistments, enlistments),
}

export default open