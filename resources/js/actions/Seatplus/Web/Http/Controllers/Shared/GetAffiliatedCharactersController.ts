import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
const GetAffiliatedCharactersController = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetAffiliatedCharactersController.url(args, options),
    method: 'get',
})

GetAffiliatedCharactersController.definition = {
    methods: ["get","head"],
    url: '/shared/affiliated/characters/{permission}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
GetAffiliatedCharactersController.url = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return GetAffiliatedCharactersController.definition.url
            .replace('{permission}', parsedArgs.permission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
GetAffiliatedCharactersController.get = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetAffiliatedCharactersController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCharactersController.php:47
* @route '/shared/affiliated/characters/{permission}'
*/
GetAffiliatedCharactersController.head = (args: { permission: string | number } | [permission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GetAffiliatedCharactersController.url(args, options),
    method: 'head',
})

export default GetAffiliatedCharactersController