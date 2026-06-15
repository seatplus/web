import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::job
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
export const job = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: job.url(options),
    method: 'post',
})

job.definition = {
    methods: ["post"],
    url: '/queue/job',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::job
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
job.url = (options?: RouteQueryOptions) => {
    return job.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::job
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
job.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: job.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
export const batch_update = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch_update.url(args, options),
    method: 'post',
})

batch_update.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/update/{character_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
batch_update.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return batch_update.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
batch_update.post = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch_update.url(args, options),
    method: 'post',
})

const dispatch = {
    job: Object.assign(job, job),
    batch_update: Object.assign(batch_update, batch_update),
}

export default dispatch