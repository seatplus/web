import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::log
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
export const log = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: log.url(args, options),
    method: 'get',
})

log.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/application/{application_id}/log',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::log
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
log.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return log.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::log
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
log.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: log.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::log
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
log.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: log.url(args, options),
    method: 'head',
})

const activity = {
    log: Object.assign(log, log),
}

export default activity