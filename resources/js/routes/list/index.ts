import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import open from './open'
import existing from './existing'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
export const users = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

users.definition = {
    methods: ["get","head"],
    url: '/acl/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
users.url = (options?: RouteQueryOptions) => {
    return users.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
users.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListUserController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListUserController.php:35
* @route '/acl/user'
*/
users.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: users.url(options),
    method: 'head',
})

const list = {
    open: Object.assign(open, open),
    existing: Object.assign(existing, existing),
    users: Object.assign(users, users),
}

export default list