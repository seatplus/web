import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::apply
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
export const apply = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::apply
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
apply.url = (options?: RouteQueryOptions) => {
    return apply.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::apply
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:53
* @route '/corporation/recruitment/apply'
*/
apply.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullCharacterApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
export const pullCharacterApplication = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: pullCharacterApplication.url(args, options),
    method: 'delete',
})

pullCharacterApplication.definition = {
    methods: ["delete"],
    url: '/corporation/recruitment/application/character/{character_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullCharacterApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
pullCharacterApplication.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return pullCharacterApplication.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullCharacterApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
pullCharacterApplication.delete = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: pullCharacterApplication.url(args, options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullUserApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
export const pullUserApplication = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: pullUserApplication.url(options),
    method: 'delete',
})

pullUserApplication.definition = {
    methods: ["delete"],
    url: '/corporation/recruitment/application/user',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullUserApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
pullUserApplication.url = (options?: RouteQueryOptions) => {
    return pullUserApplication.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::pullUserApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:67
* @route '/corporation/recruitment/application/user'
*/
pullUserApplication.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: pullUserApplication.url(options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getOpenCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
export const getOpenCorporationApplications = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOpenCorporationApplications.url(args, options),
    method: 'get',
})

getOpenCorporationApplications.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getOpenCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
getOpenCorporationApplications.url = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            decision_count: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        decision_count: args.decision_count,
    }

    return getOpenCorporationApplications.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{decision_count}', parsedArgs.decision_count.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getOpenCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
getOpenCorporationApplications.get = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOpenCorporationApplications.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getOpenCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:74
* @route '/corporation/recruitment/applications/{corporation_id}/open/{decision_count}'
*/
getOpenCorporationApplications.head = (args: { corporation_id: string | number, decision_count: string | number } | [corporation_id: string | number, decision_count: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getOpenCorporationApplications.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getClosedCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
export const getClosedCorporationApplications = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getClosedCorporationApplications.url(args, options),
    method: 'get',
})

getClosedCorporationApplications.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/applications/{corporation_id}/closed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getClosedCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
getClosedCorporationApplications.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { corporation_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
    }

    return getClosedCorporationApplications.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getClosedCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
getClosedCorporationApplications.get = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getClosedCorporationApplications.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getClosedCorporationApplications
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:87
* @route '/corporation/recruitment/applications/{corporation_id}/closed'
*/
getClosedCorporationApplications.head = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getClosedCorporationApplications.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
export const getBatchUpdate = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBatchUpdate.url(args, options),
    method: 'get',
})

getBatchUpdate.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/update/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
getBatchUpdate.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return getBatchUpdate.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
getBatchUpdate.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBatchUpdate.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
getBatchUpdate.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getBatchUpdate.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::dispatchBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
export const dispatchBatchUpdate = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dispatchBatchUpdate.url(args, options),
    method: 'post',
})

dispatchBatchUpdate.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/update/{character_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::dispatchBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
dispatchBatchUpdate.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return dispatchBatchUpdate.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::dispatchBatchUpdate
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:194
* @route '/corporation/recruitment/update/{character_id}'
*/
dispatchBatchUpdate.post = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dispatchBatchUpdate.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
export const getApplication = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApplication.url(args, options),
    method: 'get',
})

getApplication.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/application/{application_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
getApplication.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return getApplication.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
getApplication.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApplication.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
getApplication.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApplication.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::reviewApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
export const reviewApplication = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reviewApplication.url(args, options),
    method: 'post',
})

reviewApplication.definition = {
    methods: ["post"],
    url: '/corporation/recruitment/application/{application_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::reviewApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
reviewApplication.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return reviewApplication.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::reviewApplication
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:130
* @route '/corporation/recruitment/application/{application_id}'
*/
reviewApplication.post = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reviewApplication.url(args, options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::addComment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:165
* @route '/corporation/recruitment/application/{application_id}'
*/
export const addComment = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: addComment.url(args, options),
    method: 'put',
})

addComment.definition = {
    methods: ["put"],
    url: '/corporation/recruitment/application/{application_id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::addComment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:165
* @route '/corporation/recruitment/application/{application_id}'
*/
addComment.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return addComment.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::addComment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:165
* @route '/corporation/recruitment/application/{application_id}'
*/
addComment.put = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: addComment.url(args, options),
    method: 'put',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getActivityLog
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
export const getActivityLog = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getActivityLog.url(args, options),
    method: 'get',
})

getActivityLog.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/application/{application_id}/log',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getActivityLog
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
getActivityLog.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return getActivityLog.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getActivityLog
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
getActivityLog.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getActivityLog.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::getActivityLog
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:177
* @route '/corporation/recruitment/application/{application_id}/log'
*/
getActivityLog.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getActivityLog.url(args, options),
    method: 'head',
})

const ApplicationsController = { apply, pullCharacterApplication, pullUserApplication, getOpenCorporationApplications, getClosedCorporationApplications, getBatchUpdate, dispatchBatchUpdate, getApplication, reviewApplication, addComment, getActivityLog }

export default ApplicationsController