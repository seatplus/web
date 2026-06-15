import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
import character from './character'
import user from './user'
/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
export const scopes = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: scopes.url(args, options),
    method: 'delete',
})

scopes.definition = {
    methods: ["delete"],
    url: '/configuration/settings/scopes/delete/{entity_id?}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
scopes.url = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entity_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            entity_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "entity_id",
    ])

    const parsedArgs = {
        entity_id: args?.entity_id,
    }

    return scopes.definition.url
            .replace('{entity_id?}', parsedArgs.entity_id?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController::scopes
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Configuration/SsoSettings/SsoSettingsController.php:75
* @route '/configuration/settings/scopes/delete/{entity_id?}'
*/
scopes.delete = (args?: { entity_id?: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: scopes.url(args, options),
    method: 'delete',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::enlistment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:53
* @route '/corporation/recruitment/{corporation_id}'
*/
export const enlistment = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: enlistment.url(args, options),
    method: 'delete',
})

enlistment.definition = {
    methods: ["delete"],
    url: '/corporation/recruitment/{corporation_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::enlistment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:53
* @route '/corporation/recruitment/{corporation_id}'
*/
enlistment.url = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return enlistment.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::enlistment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:53
* @route '/corporation/recruitment/{corporation_id}'
*/
enlistment.delete = (args: { corporation_id: string | number } | [corporation_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: enlistment.url(args, options),
    method: 'delete',
})

const deleteMethod = {
    scopes: Object.assign(scopes, scopes),
    character: Object.assign(character, character),
    user: Object.assign(user, user),
    enlistment: Object.assign(enlistment, enlistment),
}

export default deleteMethod