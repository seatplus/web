import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:54
* @route '/corporation/wallet/{corporation_id}/journal/{division_id}'
*/
export const journal = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journal.url(args, options),
    method: 'get',
})

journal.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet/{corporation_id}/journal/{division_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:54
* @route '/corporation/wallet/{corporation_id}/journal/{division_id}'
*/
journal.url = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions) => {
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

    return journal.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{division_id}', parsedArgs.division_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:54
* @route '/corporation/wallet/{corporation_id}/journal/{division_id}'
*/
journal.get = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journal.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:54
* @route '/corporation/wallet/{corporation_id}/journal/{division_id}'
*/
journal.head = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: journal.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
export const balance = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

balance.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet/{corporation_id}/balance/{division_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.url = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions) => {
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

    return balance.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{division_id}', parsedArgs.division_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.get = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.head = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: balance.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
export const transaction = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transaction.url(args, options),
    method: 'get',
})

transaction.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet/{corporation_id}/transaction/{division_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
transaction.url = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions) => {
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

    return transaction.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{division_id}', parsedArgs.division_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
transaction.get = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transaction.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:77
* @route '/corporation/wallet/{corporation_id}/transaction/{division_id}'
*/
transaction.head = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transaction.url(args, options),
    method: 'head',
})

const CorporationWalletController = { index, journal, balance, transaction }

export default CorporationWalletController