import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
const ApproveApplicationController = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ApproveApplicationController.url(args, options),
    method: 'post',
})

ApproveApplicationController.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/approve/{user_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
ApproveApplicationController.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return ApproveApplicationController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
ApproveApplicationController.post = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ApproveApplicationController.url(args, options),
    method: 'post',
})

export default ApproveApplicationController