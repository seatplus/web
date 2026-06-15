import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
export const index = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/acl/manage_control_group/{role_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
index.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
index.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
index.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

const ManageControlGroupMembersController = { index }

export default ManageControlGroupMembersController