import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
export const detail = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet/{corporation_id}/transaction/{division_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
detail.url = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            division_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        division_id: args.division_id,
    }

    return detail.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{division_id}', parsedArgs.division_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
detail.get = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
detail.head = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

const wallet_transaction = {
    detail: Object.assign(detail, detail),
}

export default wallet_transaction