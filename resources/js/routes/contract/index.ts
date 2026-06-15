import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
export const details = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

details.definition = {
    methods: ["get","head"],
    url: '/character/contracts/{character_id}/contract/{contract_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
details.url = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
            contract_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
        contract_id: args.contract_id,
    }

    return details.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace('{contract_id}', parsedArgs.contract_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
details.get = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::details
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
details.head = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: details.url(args, options),
    method: 'head',
})

const contract = {
    details: Object.assign(details, details),
}

export default contract