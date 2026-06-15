import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
import stats from './stats'
import workload from './workload'
import masters from './masters'
import monitoring from './monitoring'
import monitoringTag from './monitoring-tag'
import jobsMetrics from './jobs-metrics'
import queuesMetrics from './queues-metrics'
import jobsBatches from './jobs-batches'
import pendingJobs from './pending-jobs'
import completedJobs from './completed-jobs'
import silencedJobs from './silenced-jobs'
import failedJobs from './failed-jobs'
import retryJobs from './retry-jobs'
import jobs from './jobs'
/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
export const index = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/{view?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.url = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { view: args }
    }

    if (Array.isArray(args)) {
        args = {
            view: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "view",
    ])

    const parsedArgs = {
        view: args?.view,
    }

    return index.definition.url
            .replace('{view?}', parsedArgs.view?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.get = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.head = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
export const status = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/queue/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
status.url = (options?: RouteQueryOptions) => {
    return status.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
status.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
status.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(options),
    method: 'head',
})

const horizon = {
    stats: Object.assign(stats, stats),
    workload: Object.assign(workload, workload),
    masters: Object.assign(masters, masters),
    monitoring: Object.assign(monitoring, monitoring),
    monitoringTag: Object.assign(monitoringTag, monitoringTag),
    jobsMetrics: Object.assign(jobsMetrics, jobsMetrics),
    queuesMetrics: Object.assign(queuesMetrics, queuesMetrics),
    jobsBatches: Object.assign(jobsBatches, jobsBatches),
    pendingJobs: Object.assign(pendingJobs, pendingJobs),
    completedJobs: Object.assign(completedJobs, completedJobs),
    silencedJobs: Object.assign(silencedJobs, silencedJobs),
    failedJobs: Object.assign(failedJobs, failedJobs),
    retryJobs: Object.assign(retryJobs, retryJobs),
    jobs: Object.assign(jobs, jobs),
    index: Object.assign(index, index),
    status: Object.assign(status, status),
}

export default horizon