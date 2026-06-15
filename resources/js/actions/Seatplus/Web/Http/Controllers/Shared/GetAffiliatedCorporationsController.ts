import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
const GetAffiliatedCorporationsController = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetAffiliatedCorporationsController.url(args, options),
    method: 'get',
})

GetAffiliatedCorporationsController.definition = {
    methods: ["get","head"],
    url: '/shared/affiliated/corporations/{permission}/{corporation_role?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
GetAffiliatedCorporationsController.url = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions) => {
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

    return GetAffiliatedCorporationsController.definition.url
            .replace('{permission}', parsedArgs.permission.toString())
            .replace('{corporation_role?}', parsedArgs.corporation_role?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
GetAffiliatedCorporationsController.get = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetAffiliatedCorporationsController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/GetAffiliatedCorporationsController.php:37
* @route '/shared/affiliated/corporations/{permission}/{corporation_role?}'
*/
GetAffiliatedCorporationsController.head = (args: { permission: string | number, corporation_role?: string | number } | [permission: string | number, corporation_role: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GetAffiliatedCorporationsController.url(args, options),
    method: 'head',
})

export default GetAffiliatedCorporationsController