import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::watchlist
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:70
* @route '/corporation/recruitment/watchlist/{corporation_id}'
*/
export const watchlist = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: watchlist.url(args, options),
    method: 'post',
})

watchlist.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/watchlist/{corporation_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::watchlist
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:70
* @route '/corporation/recruitment/watchlist/{corporation_id}'
*/
watchlist.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { corporation_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
    }

    return watchlist.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::watchlist
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:70
* @route '/corporation/recruitment/watchlist/{corporation_id}'
*/
watchlist.post = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: watchlist.url(args, options),
    method: 'post',
})

const update = {
    watchlist: Object.assign(watchlist, watchlist),
}

export default update