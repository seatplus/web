import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
export const detail = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/journal',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
detail.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return detail.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
detail.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
detail.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

const wallet_journal = {
    detail: Object.assign(detail, detail),
}

export default wallet_journal