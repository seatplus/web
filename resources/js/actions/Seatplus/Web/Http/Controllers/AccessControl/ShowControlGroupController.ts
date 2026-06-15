import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
const ShowControlGroupController = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowControlGroupController.url(args, options),
    method: 'get',
})

ShowControlGroupController.definition = {
    methods: ["get","head"],
    url: '/acl/acl/{role_id}/detail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
ShowControlGroupController.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ShowControlGroupController.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
ShowControlGroupController.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowControlGroupController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
ShowControlGroupController.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ShowControlGroupController.url(args, options),
    method: 'head',
})

export default ShowControlGroupController