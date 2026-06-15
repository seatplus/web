import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
const SchedulesCreate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SchedulesCreate.url(options),
    method: 'get',
})

SchedulesCreate.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
SchedulesCreate.url = (options?: RouteQueryOptions) => {
    return SchedulesCreate.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
SchedulesCreate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SchedulesCreate.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
SchedulesCreate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SchedulesCreate.url(options),
    method: 'head',
})

export default SchedulesCreate