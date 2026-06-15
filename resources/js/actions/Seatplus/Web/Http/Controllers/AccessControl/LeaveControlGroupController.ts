import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
const LeaveControlGroupController = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: LeaveControlGroupController.url(args, options),
    method: 'delete',
})

LeaveControlGroupController.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}/user/{user_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
LeaveControlGroupController.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            role_id: args[0],
            user_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role_id: args.role_id,
        user_id: args.user_id,
    }

    return LeaveControlGroupController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
LeaveControlGroupController.delete = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: LeaveControlGroupController.url(args, options),
    method: 'delete',
})

export default LeaveControlGroupController