import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::character_affiliation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
export const character_affiliation = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: character_affiliation.url(options),
    method: 'post',
})

character_affiliation.definition = {
    methods: ["post"],
    url: '/shared/resolve/character_affiliations',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::character_affiliation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
character_affiliation.url = (options?: RouteQueryOptions) => {
    return character_affiliation.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::character_affiliation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:58
* @route '/shared/resolve/character_affiliations'
*/
character_affiliation.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: character_affiliation.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::corporation_info
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
export const corporation_info = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: corporation_info.url(args, options),
    method: 'get',
})

corporation_info.definition = {
    methods: ["get","head"],
    url: '/shared/resolve/{corporation_id}/corporation_info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::corporation_info
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
corporation_info.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return corporation_info.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::corporation_info
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
corporation_info.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: corporation_info.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::corporation_info
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:65
* @route '/shared/resolve/{corporation_id}/corporation_info'
*/
corporation_info.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: corporation_info.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::id
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
export const id = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: id.url(args, options),
    method: 'get',
})

id.definition = {
    methods: ["get","head"],
    url: '/shared/resolve/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::id
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
id.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return id.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::id
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
id.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: id.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::id
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:72
* @route '/shared/resolve/{id}'
*/
id.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: id.url(args, options),
    method: 'head',
})

const resolve = {
    ids: Object.assign(ids, ids),
    character_affiliation: Object.assign(character_affiliation, character_affiliation),
    corporation_info: Object.assign(corporation_info, corporation_info),
    id: Object.assign(id, id),
}

export default resolve