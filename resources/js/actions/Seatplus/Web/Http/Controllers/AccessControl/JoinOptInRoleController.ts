import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
const JoinOptInRoleController = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: JoinOptInRoleController.url(args, options),
    method: 'post',
})

JoinOptInRoleController.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/join',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
JoinOptInRoleController.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return JoinOptInRoleController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
JoinOptInRoleController.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: JoinOptInRoleController.url(args, options),
    method: 'post',
})

export default JoinOptInRoleController