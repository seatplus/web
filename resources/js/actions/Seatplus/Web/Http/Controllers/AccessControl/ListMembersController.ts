import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
const ListMembersController = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMembersController.url(args, options),
    method: 'get',
})

ListMembersController.definition = {
    methods: ["get","head"],
    url: '/acl/acl/{role_id}/members',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
ListMembersController.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            role_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role_id: args.role_id,
    }

    return ListMembersController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
ListMembersController.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMembersController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
ListMembersController.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListMembersController.url(args, options),
    method: 'head',
})

export default ListMembersController