import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
export const characters = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: characters.url(args, options),
    method: 'get',
})

characters.definition = {
    methods: ["get","head"],
    url: '/shared/affiliated/characters/{permission}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
characters.url = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { permission: args }
    }

    if (Array.isArray(args)) {
        args = {
            permission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        permission: args.permission,
    }

    return characters.definition.url
            .replace('{permission}', parsedArgs.permission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
characters.get = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: characters.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
characters.head = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: characters.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
export const corporations = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: corporations.url(args, options),
    method: 'get',
})

corporations.definition = {
    methods: ["get","head"],
    url: '/shared/affiliated/corporations/{permission}/{corporation_role?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
corporations.url = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            permission: args[0],
            corporation_role: args[1],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "corporation_role",
    ])

    const parsedArgs = {
        permission: args.permission,
        corporation_role: args.corporation_role,
    }

    return corporations.definition.url
            .replace('{permission}', parsedArgs.permission.toString())
            .replace('{corporation_role?}', parsedArgs.corporation_role?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
corporations.get = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: corporations.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
corporations.head = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: corporations.url(args, options),
    method: 'head',
})

const affiliated = {
    characters: Object.assign(characters, characters),
    corporations: Object.assign(corporations, corporations),
}

export default affiliated