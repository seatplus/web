import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
export const application = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: application.url(args, options),
    method: 'post',
})

application.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/application/{application_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
application.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return application.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
application.post = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: application.url(args, options),
    method: 'post',
})

const review = {
    application: Object.assign(application, application),
}

export default review