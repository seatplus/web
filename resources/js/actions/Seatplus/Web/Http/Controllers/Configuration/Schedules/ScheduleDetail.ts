import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
const ScheduleDetail = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ScheduleDetail.url(args, options),
    method: 'get',
})

ScheduleDetail.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules/{schedule_id}/details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
ScheduleDetail.url = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            schedule_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule_id: args.schedule_id,
    }

    return ScheduleDetail.definition.url
            .replace('{schedule_id}', parsedArgs.schedule_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
ScheduleDetail.get = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ScheduleDetail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
ScheduleDetail.head = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ScheduleDetail.url(args, options),
    method: 'head',
})

export default ScheduleDetail