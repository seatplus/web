import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::getCorporationCompliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
export const getCorporationCompliance = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCorporationCompliance.url(args, options),
    method: 'get',
})

getCorporationCompliance.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance/{corporation_id}/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::getCorporationCompliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
getCorporationCompliance.url = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            type: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        type: args.type,
    }

    return getCorporationCompliance.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::getCorporationCompliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
getCorporationCompliance.get = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCorporationCompliance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::getCorporationCompliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
getCorporationCompliance.head = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCorporationCompliance.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::reviewUser
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
export const reviewUser = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviewUser.url(args, options),
    method: 'get',
})

reviewUser.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance/{corporation_id}/review/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::reviewUser
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
reviewUser.url = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            user: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return reviewUser.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::reviewUser
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
reviewUser.get = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviewUser.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::reviewUser
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
reviewUser.head = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reviewUser.url(args, options),
    method: 'head',
})

const MemberComplianceController = { index, getCorporationCompliance, reviewUser }

export default MemberComplianceController