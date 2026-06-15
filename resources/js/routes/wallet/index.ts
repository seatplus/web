import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
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

const wallet = {
    journalTypes: Object.assign(journalTypes, journalTypes),
}

export default wallet