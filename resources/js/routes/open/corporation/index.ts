import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
export const applications = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applications.url(args, options),
    method: 'get',
})

applications.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
applications.url = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            decision_count: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        decision_count: args.decision_count,
    }

    return applications.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{decision_count}', parsedArgs.decision_count.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
applications.get = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applications.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::applications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
applications.head = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: applications.url(args, options),
    method: 'head',
})

const corporation = {
    applications: Object.assign(applications, applications),
}

export default corporation