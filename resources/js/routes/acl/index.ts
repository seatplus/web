import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import search from './search'
import update from './update'
import moderator from './moderator'
import member from './member'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
export const groups = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: groups.url(options),
    method: 'get',
})

groups.definition = {
    methods: ["get","head"],
    url: '/acl',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
groups.url = (options?: RouteQueryOptions) => {
    return groups.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
groups.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: groups.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupsController.php:12
* @route '/acl'
*/
groups.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: groups.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
export const members = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: members.url(args, options),
    method: 'get',
})

members.definition = {
    methods: ["get","head"],
    url: '/acl/acl/{role_id}/members',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
members.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return members.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
members.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: members.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListMembersController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListMembersController.php:44
* @route '/acl/acl/{role_id}/members'
*/
members.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: members.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApplyToRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApplyToRoleController.php:15
* @route '/acl/acl/{role_id}/apply'
*/
export const apply = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApplyToRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApplyToRoleController.php:15
* @route '/acl/acl/{role_id}/apply'
*/
apply.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return apply.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApplyToRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApplyToRoleController.php:15
* @route '/acl/acl/{role_id}/apply'
*/
apply.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
export const join = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: join.url(args, options),
    method: 'post',
})

join.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/join',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
join.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return join.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/JoinOptInRoleController.php:20
* @route '/acl/acl/{role_id}/join'
*/
join.post = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: join.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
export const approve = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/acl/acl/{role_id}/approve/{user_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
approve.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return approve.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ApproveApplicationController.php:19
* @route '/acl/acl/{role_id}/approve/{user_id}'
*/
approve.post = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DenyApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DenyApplicationController.php:19
* @route '/acl/acl/{role_id}/deny/{user_id}'
*/
export const deny = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(args, options),
    method: 'delete',
})

deny.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}/deny/{user_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DenyApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DenyApplicationController.php:19
* @route '/acl/acl/{role_id}/deny/{user_id}'
*/
deny.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return deny.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DenyApplicationController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DenyApplicationController.php:19
* @route '/acl/acl/{role_id}/deny/{user_id}'
*/
deny.delete = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(args, options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
export const leave = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: leave.url(args, options),
    method: 'delete',
})

leave.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}/user/{user_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
leave.url = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions) => {
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

    return leave.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace('{user_id}', parsedArgs.user_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/LeaveControlGroupController.php:46
* @route '/acl/acl/{role_id}/user/{user_id}'
*/
leave.delete = (args: { role_id: string | number, user_id: string | number } | [role_id: string | number, user_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: leave.url(args, options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/acl/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
export const deleteMethod = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/acl/acl/{role_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
deleteMethod.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/DeleteControlGroupController.php:34
* @route '/acl/acl/{role_id}'
*/
deleteMethod.delete = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::manage
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
export const manage = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manage.url(args, options),
    method: 'get',
})

manage.definition = {
    methods: ["get","head"],
    url: '/acl/manage_control_group/{role_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::manage
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
manage.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return manage.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::manage
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
manage.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manage.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController::manage
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ManageControlGroupMembersController.php:40
* @route '/acl/manage_control_group/{role_id}'
*/
manage.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manage.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
export const detail = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/acl/acl/{role_id}/detail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
detail.url = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return detail.definition.url
            .replace('{role_id}', parsedArgs.role_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
detail.get = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ShowControlGroupController.php:30
* @route '/acl/acl/{role_id}/detail'
*/
detail.head = (args: { role_id: string | number } | [role_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

const acl = {
    groups: Object.assign(groups, groups),
    members: Object.assign(members, members),
    apply: Object.assign(apply, apply),
    join: Object.assign(join, join),
    approve: Object.assign(approve, approve),
    deny: Object.assign(deny, deny),
    leave: Object.assign(leave, leave),
    create: Object.assign(create, create),
    delete: Object.assign(deleteMethod, deleteMethod),
    search: Object.assign(search, search),
    manage: Object.assign(manage, manage),
    detail: Object.assign(detail, detail),
    update: Object.assign(update, update),
    moderator: Object.assign(moderator, moderator),
    member: Object.assign(member, member),
}

export default acl