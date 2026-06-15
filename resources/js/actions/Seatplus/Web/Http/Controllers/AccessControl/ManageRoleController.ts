import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
const ManageRoleControlleraad6615ba2b293ef9af603be34462a25 = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControlleraad6615ba2b293ef9af603be34462a25.url(args, options),
    method: 'post',
})

ManageRoleControlleraad6615ba2b293ef9af603be34462a25.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/automatic',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
ManageRoleControlleraad6615ba2b293ef9af603be34462a25.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ManageRoleControlleraad6615ba2b293ef9af603be34462a25.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
ManageRoleControlleraad6615ba2b293ef9af603be34462a25.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControlleraad6615ba2b293ef9af603be34462a25.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
const ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.url(args, options),
    method: 'post',
})

ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/manual',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
const ManageRoleControllerb594044f795d3650e9cc5c18f074b5af = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.url(args, options),
    method: 'post',
})

ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/on-request',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControllerb594044f795d3650e9cc5c18f074b5af.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
const ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.url(args, options),
    method: 'post',
})

ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/opt-in',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e.url(args, options),
    method: 'post',
})

const ManageRoleController = {
    '/acl/acl/{role_id}/automatic': ManageRoleControlleraad6615ba2b293ef9af603be34462a25,
    '/acl/acl/{role_id}/manual': ManageRoleController5ff3b17afa18422cfdd3053dbd575a3f,
    '/acl/acl/{role_id}/on-request': ManageRoleControllerb594044f795d3650e9cc5c18f074b5af,
    '/acl/acl/{role_id}/opt-in': ManageRoleControllerdbe2be6130da189abe8c2847f9bd514e,
}

export default ManageRoleController