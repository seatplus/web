import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageMembersController.php:36
* @route '/acl/acl/{role_id}/manage_members'
*/
const ManageMembersController = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ManageMembersController.url(args, options),
    method: 'get',
})

ManageMembersController.definition = {
    methods: ["get","head"],
    url: '/acl/acl/{role_id}/manage_members',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageMembersController.php:36
* @route '/acl/acl/{role_id}/manage_members'
*/
ManageMembersController.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ManageMembersController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageMembersController.php:36
* @route '/acl/acl/{role_id}/manage_members'
*/
ManageMembersController.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ManageMembersController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageMembersController.php:36
* @route '/acl/acl/{role_id}/manage_members'
*/
ManageMembersController.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ManageMembersController.url(args, options),
    method: 'head',
})

export default ManageMembersController