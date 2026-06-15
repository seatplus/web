import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getEntities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
export const getEntities = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getEntities.url(options),
    method: 'post',
})

getEntities.definition = {
    methods: ["post"],
    url: '/queue/manual_job/entities',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getEntities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
getEntities.url = (options?: RouteQueryOptions) => {
    return getEntities.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getEntities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
getEntities.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getEntities.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getBatchStatus
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
export const getBatchStatus = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBatchStatus.url(args, options),
    method: 'get',
})

getBatchStatus.definition = {
    methods: ["get","head"],
    url: '/queue/{batch_id}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getBatchStatus
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
getBatchStatus.url = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { batch_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            batch_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        batch_id: args.batch_id,
    }

    return getBatchStatus.definition.url
            .replace('{batch_id}', parsedArgs.batch_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getBatchStatus
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
getBatchStatus.get = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBatchStatus.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::getBatchStatus
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
getBatchStatus.head = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getBatchStatus.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::dispatch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
export const dispatch = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dispatch.url(options),
    method: 'post',
})

dispatch.definition = {
    methods: ["post"],
    url: '/queue/job',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::dispatch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
dispatch.url = (options?: RouteQueryOptions) => {
    return dispatch.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::dispatch
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:59
* @route '/queue/job'
*/
dispatch.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dispatch.url(options),
    method: 'post',
})

const DispatchJobController = { getEntities, getBatchStatus, dispatch }

export default DispatchJobController