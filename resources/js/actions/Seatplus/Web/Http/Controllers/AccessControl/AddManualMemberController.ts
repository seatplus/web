import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\AddManualMemberController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/AddManualMemberController.php:18
* @route '/acl/acl/{role_id}/member/{user_id}'
*/
const AddManualMemberController = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AddManualMemberController.url(args, options),
    method: 'post',
})

AddManualMemberController.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/member/{user_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\AddManualMemberController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/AddManualMemberController.php:18
* @route '/acl/acl/{role_id}/member/{user_id}'
*/
AddManualMemberController.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return AddManualMemberController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\AddManualMemberController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/AddManualMemberController.php:18
* @route '/acl/acl/{role_id}/member/{user_id}'
*/
AddManualMemberController.post = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AddManualMemberController.url(args, options),
    method: 'post',
})

export default AddManualMemberController