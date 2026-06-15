import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
export const details = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

details.definition = {
    methods: ["get","head"],
    url: '/character/contracts/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
details.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return details.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
details.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
details.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: details.url(args, options),
    method: 'head',
})

const contracts = {
    details: Object.assign(details, details),
}

export default contracts