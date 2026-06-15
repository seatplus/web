import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
const SchedulesDelete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: SchedulesDelete.url(args, options),
    method: 'delete',
})

SchedulesDelete.definition = {
    methods: ["delete"],
    url: '/configuration/schedules/{id}/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
SchedulesDelete.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return SchedulesDelete.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
SchedulesDelete.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: SchedulesDelete.url(args, options),
    method: 'delete',
})

export default SchedulesDelete