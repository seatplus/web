import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
const SchedulesIndex = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SchedulesIndex.url(options),
    method: 'get',
})

SchedulesIndex.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
SchedulesIndex.url = (options?: RouteQueryOptions) => {
    return SchedulesIndex.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
SchedulesIndex.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SchedulesIndex.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
SchedulesIndex.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SchedulesIndex.url(options),
    method: 'head',
})

export default SchedulesIndex