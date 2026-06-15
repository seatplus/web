import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::ids
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:51
* @route '/shared/resolve/ids'
*/
export const ids = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ids.url(options),
    method: 'post',
})

ids.definition = {
    methods: ["post"],
    url: '/shared/resolve/ids',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::ids
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:51
* @route '/shared/resolve/ids'
*/
ids.url = (options?: RouteQueryOptions) => {
    return ids.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::ids
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:51
* @route '/shared/resolve/ids'
*/
ids.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ids.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::characterAffiliations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
export const characterAffiliations = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: characterAffiliations.url(options),
    method: 'post',
})

characterAffiliations.definition = {
    methods: ["post"],
    url: '/shared/resolve/character_affiliations',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::characterAffiliations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
characterAffiliations.url = (options?: RouteQueryOptions) => {
    return characterAffiliations.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::characterAffiliations
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
characterAffiliations.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: characterAffiliations.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getCorporationInfo
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
export const getCorporationInfo = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCorporationInfo.url(args, options),
    method: 'get',
})

getCorporationInfo.definition = {
    methods: ["get","head"],
    url: '/shared/resolve/{corporation_id}/corporation_info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getCorporationInfo
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
getCorporationInfo.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { corporation_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
    }

    return getCorporationInfo.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getCorporationInfo
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
getCorporationInfo.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCorporationInfo.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getCorporationInfo
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
getCorporationInfo.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCorporationInfo.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getEntityFromId
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
export const getEntityFromId = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEntityFromId.url(args, options),
    method: 'get',
})

getEntityFromId.definition = {
    methods: ["get","head"],
    url: '/shared/resolve/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getEntityFromId
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
getEntityFromId.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return getEntityFromId.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getEntityFromId
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
getEntityFromId.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEntityFromId.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getEntityFromId
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
getEntityFromId.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEntityFromId.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::esiSearch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
export const esiSearch = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: esiSearch.url(options),
    method: 'get',
})

esiSearch.definition = {
    methods: ["get","head"],
    url: '/shared/autosuggest/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::esiSearch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
esiSearch.url = (options?: RouteQueryOptions) => {
    return esiSearch.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::esiSearch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
esiSearch.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: esiSearch.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::esiSearch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
esiSearch.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: esiSearch.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupsOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
export const typesOrGroupsOrCategories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: typesOrGroupsOrCategories.url(options),
    method: 'get',
})

typesOrGroupsOrCategories.definition = {
    methods: ["get","head"],
    url: '/shared/autosuggest/typesOrGroupOrCategories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupsOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupsOrCategories.url = (options?: RouteQueryOptions) => {
    return typesOrGroupsOrCategories.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupsOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupsOrCategories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: typesOrGroupsOrCategories.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupsOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupsOrCategories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: typesOrGroupsOrCategories.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
export const token = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: token.url(options),
    method: 'get',
})

token.definition = {
    methods: ["get","head"],
    url: '/shared/esi-search/token',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.url = (options?: RouteQueryOptions) => {
    return token.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: token.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: token.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getResourceVariants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
export const getResourceVariants = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getResourceVariants.url(args, options),
    method: 'get',
})

getResourceVariants.definition = {
    methods: ["get","head"],
    url: '/shared/image/variants/{resource_type}/{resource_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getResourceVariants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
getResourceVariants.url = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            resource_type: args[0],
            resource_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        resource_type: args.resource_type,
        resource_id: args.resource_id,
    }

    return getResourceVariants.definition.url
            .replace('{resource_type}', parsedArgs.resource_type.toString())
            .replace('{resource_id}', parsedArgs.resource_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getResourceVariants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
getResourceVariants.get = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getResourceVariants.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getResourceVariants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
getResourceVariants.head = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getResourceVariants.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getMarketsPrices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
export const getMarketsPrices = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMarketsPrices.url(options),
    method: 'get',
})

getMarketsPrices.definition = {
    methods: ["get","head"],
    url: '/shared/markets/prices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getMarketsPrices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
getMarketsPrices.url = (options?: RouteQueryOptions) => {
    return getMarketsPrices.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getMarketsPrices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
getMarketsPrices.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMarketsPrices.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::getMarketsPrices
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:164
* @route '/shared/markets/prices'
*/
getMarketsPrices.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMarketsPrices.url(options),
    method: 'head',
})

const HelperController = { ids, characterAffiliations, getCorporationInfo, getEntityFromId, esiSearch, typesOrGroupsOrCategories, token, getResourceVariants, getMarketsPrices }

export default HelperController