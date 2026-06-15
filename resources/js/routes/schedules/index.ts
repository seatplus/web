import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesIndex.php:35
* @route '/configuration/schedules'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
export const updateOrCreate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrCreate.url(options),
    method: 'post',
})

updateOrCreate.definition = {
    methods: ["post"],
    url: '/configuration/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
updateOrCreate.url = (options?: RouteQueryOptions) => {
    return updateOrCreate.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesPost.php:35
* @route '/configuration/schedules'
*/
updateOrCreate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrCreate.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesCreate.php:34
* @route '/configuration/schedules/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
export const details = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

details.definition = {
    methods: ["get","head"],
    url: '/configuration/schedules/{schedule_id}/details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
details.url = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return details.definition.url
            .replace('{schedule_id}', parsedArgs.schedule_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
details.get = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/ScheduleDetail.php:36
* @route '/configuration/schedules/{schedule_id}/details'
*/
details.head = (args: { schedule_id: string | number } | [schedule_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: details.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
export const deleteMethod = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/configuration/schedules/{id}/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
deleteMethod.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/Schedules/SchedulesDelete.php:34
* @route '/configuration/schedules/{id}/delete'
*/
deleteMethod.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const schedules = {
    index: Object.assign(index, index),
    updateOrCreate: Object.assign(updateOrCreate, updateOrCreate),
    create: Object.assign(create, create),
    details: Object.assign(details, details),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default schedules