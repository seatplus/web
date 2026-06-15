import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\CommandsController::clear
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/CommandsController.php:35
* @route '/configuration/cache/clear'
*/
export const clear = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clear.url(options),
    method: 'post',
})

clear.definition = {
    methods: ["post"],
    url: '/configuration/cache/clear',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\CommandsController::clear
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/CommandsController.php:35
* @route '/configuration/cache/clear'
*/
clear.url = (options?: RouteQueryOptions) => {
    return clear.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\CommandsController::clear
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/CommandsController.php:35
* @route '/configuration/cache/clear'
*/
clear.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clear.url(options),
    method: 'post',
})

const cache = {
    clear: Object.assign(clear, clear),
}

export default cache