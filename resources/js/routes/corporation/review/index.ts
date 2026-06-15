import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::user
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
export const user = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: user.url(args, options),
    method: 'get',
})

user.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance/{corporation_id}/review/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::user
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
user.url = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
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

    return user.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::user
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
user.get = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: user.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::user
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:96
* @route '/corporation/compliance/{corporation_id}/review/{user}'
*/
user.head = (args: { corporation_id: string | number, user: string | number | { id: string | number } } | [corporation_id: string | number, user: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: user.url(args, options),
    method: 'head',
})

const review = {
    user: Object.assign(user, user),
}

export default review