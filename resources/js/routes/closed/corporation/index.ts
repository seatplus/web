import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
export const applications = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applications.url(args, options),
    method: 'get',
})

applications.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/applications/{corporation_id}/closed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
applications.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { corporation_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
    }

    return applications.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
applications.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applications.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
applications.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: applications.url(args, options),
    method: 'head',
})

const corporation = {
    applications: Object.assign(applications, applications),
}

export default corporation