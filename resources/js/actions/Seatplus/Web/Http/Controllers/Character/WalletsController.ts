import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/wallets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
export const journal = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journal.url(args, options),
    method: 'get',
})

journal.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/journal',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
journal.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return journal.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
journal.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journal.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journal
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:57
* @route '/character/wallets/{character_id}/journal'
*/
journal.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: journal.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
export const balance = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

balance.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/balance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return balance.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: balance.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
export const transaction = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transaction.url(args, options),
    method: 'get',
})

transaction.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/transaction',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
transaction.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return transaction.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
transaction.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transaction.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::transaction
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:101
* @route '/character/wallets/{character_id}/transaction'
*/
transaction.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transaction.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journalTypes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:69
* @route '/character/wallets/ref_type'
*/
export const journalTypes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journalTypes.url(options),
    method: 'get',
})

journalTypes.definition = {
    methods: ["get","head"],
    url: '/character/wallets/ref_type',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journalTypes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:69
* @route '/character/wallets/ref_type'
*/
journalTypes.url = (options?: RouteQueryOptions) => {
    return journalTypes.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journalTypes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:69
* @route '/character/wallets/ref_type'
*/
journalTypes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: journalTypes.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::journalTypes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:69
* @route '/character/wallets/ref_type'
*/
journalTypes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: journalTypes.url(options),
    method: 'head',
})

const WalletsController = { index, journal, balance, transaction, journalTypes }

export default WalletsController