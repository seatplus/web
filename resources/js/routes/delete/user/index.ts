import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
export const application = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: application.url(options),
    method: 'delete',
})

application.definition = {
    methods: ["delete"],
    url: '/corporation/recruitment/application/user',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
application.url = (options?: RouteQueryOptions) => {
    return application.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
application.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: application.url(options),
    method: 'delete',
})

const user = {
    application: Object.assign(application, application),
}

export default user