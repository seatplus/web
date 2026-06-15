import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
export const member_tracking = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_tracking.url(args, options),
    method: 'get',
})

member_tracking.definition = {
    methods: ["get","head"],
    url: '/corporation/tracking/members/{corporation_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
member_tracking.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return member_tracking.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
member_tracking.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_tracking.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
member_tracking.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: member_tracking.url(args, options),
    method: 'head',
})

const corporation = {
    member_tracking: Object.assign(member_tracking, member_tracking),
}

export default corporation