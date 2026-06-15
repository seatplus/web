import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/assets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::getLocations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
export const getLocations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLocations.url(options),
    method: 'get',
})

getLocations.definition = {
    methods: ["get","head"],
    url: '/character/assets/locations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::getLocations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
getLocations.url = (options?: RouteQueryOptions) => {
    return getLocations.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::getLocations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
getLocations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLocations.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::getLocations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:54
* @route '/character/assets/locations'
*/
getLocations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLocations.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
export const item = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: item.url(args, options),
    method: 'get',
})

item.definition = {
    methods: ["get","head"],
    url: '/character/assets/{character_id}/item/{item_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.url = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
            item_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
        item_id: args.item_id,
    }

    return item.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace('{item_id}', parsedArgs.item_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.get = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: item.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.head = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: item.url(args, options),
    method: 'head',
})

const AssetsController = { index, getLocations, item }

export default AssetsController