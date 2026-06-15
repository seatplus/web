import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
const SchedulesPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SchedulesPost.url(options),
    method: 'post',
})

SchedulesPost.definition = {
    methods: ["post"],
    url: '/configuration/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
SchedulesPost.url = (options?: RouteQueryOptions) => {
    return SchedulesPost.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
SchedulesPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SchedulesPost.url(options),
    method: 'post',
})

export default SchedulesPost