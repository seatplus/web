import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
const ListControlGroupsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListControlGroupsController.url(options),
    method: 'get',
})

ListControlGroupsController.definition = {
    methods: ["get","head"],
    url: '/acl/acl',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
ListControlGroupsController.url = (options?: RouteQueryOptions) => {
    return ListControlGroupsController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
ListControlGroupsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListControlGroupsController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
ListControlGroupsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListControlGroupsController.url(options),
    method: 'head',
})

export default ListControlGroupsController