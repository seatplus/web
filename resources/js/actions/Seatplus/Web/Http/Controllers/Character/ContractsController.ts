import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/contracts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getCharacterContractsDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
export const getCharacterContractsDetails = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCharacterContractsDetails.url(args, options),
    method: 'get',
})

getCharacterContractsDetails.definition = {
    methods: ["get","head"],
    url: '/character/contracts/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getCharacterContractsDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
getCharacterContractsDetails.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getCharacterContractsDetails.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getCharacterContractsDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
getCharacterContractsDetails.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCharacterContractsDetails.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getCharacterContractsDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:62
* @route '/character/contracts/{character_id}'
*/
getCharacterContractsDetails.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCharacterContractsDetails.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getContractDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
export const getContractDetails = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContractDetails.url(args, options),
    method: 'get',
})

getContractDetails.definition = {
    methods: ["get","head"],
    url: '/character/contracts/{character_id}/contract/{contract_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getContractDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
getContractDetails.url = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions) => {
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

    return getContractDetails.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace('{contract_id}', parsedArgs.contract_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getContractDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
getContractDetails.get = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContractDetails.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::getContractDetails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:72
* @route '/character/contracts/{character_id}/contract/{contract_id}'
*/
getContractDetails.head = (args: { character_id: string | number, contract_id: string | number } | [character_id: string | number, contract_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getContractDetails.url(args, options),
    method: 'head',
})

const ContractsController = { index, getCharacterContractsDetails, getContractDetails }

export default ContractsController