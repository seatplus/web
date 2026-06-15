import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
export const index = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/corporation_history/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
index.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
index.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
index.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

const CorporationHistoryController = { index }

export default CorporationHistoryController