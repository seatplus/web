import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
const DeleteControlGroupController = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: DeleteControlGroupController.url(args, options),
    method: 'delete',
})

DeleteControlGroupController.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
DeleteControlGroupController.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return DeleteControlGroupController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
DeleteControlGroupController.delete = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: DeleteControlGroupController.url(args, options),
    method: 'delete',
})

export default DeleteControlGroupController