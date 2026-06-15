import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
const ListUserController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListUserController.url(options),
    method: 'get',
})

ListUserController.definition = {
    methods: ["get","head"],
    url: '/acl/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
ListUserController.url = (options?: RouteQueryOptions) => {
    return ListUserController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
ListUserController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListUserController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
ListUserController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListUserController.url(options),
    method: 'head',
})

export default ListUserController