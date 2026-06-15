import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
export const detail = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/transaction',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
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
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
detail.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
detail.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

const wallet_transaction = {
    detail: Object.assign(detail, detail),
}

export default wallet_transaction