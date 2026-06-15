import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
export const application = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: application.url(options),
    method: 'post',
})

application.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
application.url = (options?: RouteQueryOptions) => {
    return application.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
application.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: application.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
export const manual_location = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manual_location.url(options),
    method: 'post',
})

manual_location.definition = {
    methods: ["post"],
    url: '/shared/location',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
manual_location.url = (options?: RouteQueryOptions) => {
    return manual_location.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
manual_location.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manual_location.url(options),
    method: 'post',
})

const post = {
    application: Object.assign(application, application),
    manual_location: Object.assign(manual_location, manual_location),
}

export default post