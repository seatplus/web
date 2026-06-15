import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
export const automatic = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: automatic.url(args, options),
    method: 'post',
})

automatic.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/automatic',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
automatic.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return automatic.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/automatic'
*/
automatic.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: automatic.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
export const manual = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manual.url(args, options),
    method: 'post',
})

manual.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/manual',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
manual.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return manual.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/manual'
*/
manual.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manual.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
export const onRequest = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: onRequest.url(args, options),
    method: 'post',
})

onRequest.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/on-request',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
onRequest.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return onRequest.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/on-request'
*/
onRequest.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: onRequest.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
export const optIn = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: optIn.url(args, options),
    method: 'post',
})

optIn.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/opt-in',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
optIn.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return optIn.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageRoleController.php:24
* @route '/acl/acl/{role_id}/opt-in'
*/
optIn.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: optIn.url(args, options),
    method: 'post',
})

const update = {
    automatic: Object.assign(automatic, automatic),
    manual: Object.assign(manual, manual),
    onRequest: Object.assign(onRequest, onRequest),
    optIn: Object.assign(optIn, optIn),
}

export default update