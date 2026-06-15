import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/corporation/tracking',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::getMemberTracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
export const getMemberTracking = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMemberTracking.url(args, options),
    method: 'get',
})

getMemberTracking.definition = {
    methods: ["get","head"],
    url: '/corporation/tracking/members/{corporation_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::getMemberTracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
getMemberTracking.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMemberTracking.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::getMemberTracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
getMemberTracking.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMemberTracking.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::getMemberTracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:57
* @route '/corporation/tracking/members/{corporation_id}'
*/
getMemberTracking.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMemberTracking.url(args, options),
    method: 'head',
})

const MemberTrackingController = { index, getMemberTracking }

export default MemberTrackingController