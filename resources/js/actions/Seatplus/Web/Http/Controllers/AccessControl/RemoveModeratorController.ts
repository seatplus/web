import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\RemoveModeratorController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/RemoveModeratorController.php:21
* @route '/acl/acl/{role_id}/moderator/{user_id}'
*/
const RemoveModeratorController = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RemoveModeratorController.url(args, options),
    method: 'delete',
})

RemoveModeratorController.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}/moderator/{user_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\RemoveModeratorController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/RemoveModeratorController.php:21
* @route '/acl/acl/{role_id}/moderator/{user_id}'
*/
RemoveModeratorController.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return RemoveModeratorController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\RemoveModeratorController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/RemoveModeratorController.php:21
* @route '/acl/acl/{role_id}/moderator/{user_id}'
*/
RemoveModeratorController.delete = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RemoveModeratorController.url(args, options),
    method: 'delete',
})

export default RemoveModeratorController