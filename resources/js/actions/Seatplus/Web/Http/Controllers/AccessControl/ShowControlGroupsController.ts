import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
const ShowControlGroupsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowControlGroupsController.url(options),
    method: 'get',
})

ShowControlGroupsController.definition = {
    methods: ["get","head"],
    url: '/acl',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
ShowControlGroupsController.url = (options?: RouteQueryOptions) => {
    return ShowControlGroupsController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
ShowControlGroupsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowControlGroupsController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
ShowControlGroupsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ShowControlGroupsController.url(options),
    method: 'head',
})

export default ShowControlGroupsController