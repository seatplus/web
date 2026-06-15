import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import manuel_locations from './manuel_locations'
import character from './character'
import mail14724b from './mail'
import corporation from './corporation'
import activity from './activity'
import affiliated from './affiliated'
import resource from './resource'
import markets from './markets'
/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::batch_status
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
export const batch_status = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: batch_status.url(args, options),
    method: 'get',
})

batch_status.definition = {
    methods: ["get","head"],
    url: '/queue/{batch_id}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::batch_status
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
batch_status.url = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { batch_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            batch_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        batch_id: args.batch_id,
    }

    return batch_status.definition.url
            .replace('{batch_id}', parsedArgs.batch_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::batch_status
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
batch_status.get = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: batch_status.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::batch_status
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:157
* @route '/queue/{batch_id}/status'
*/
batch_status.head = (args: { batch_id: string | number } | [batch_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: batch_status.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
export const mail = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mail.url(args, options),
    method: 'get',
})

mail.definition = {
    methods: ["get","head"],
    url: '/character/mails/content/{mail_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
mail.url = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { mail_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            mail_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        mail_id: args.mail_id,
    }

    return mail.definition.url
            .replace('{mail_id}', parsedArgs.mail_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
mail.get = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
mail.head = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mail.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
export const batch_update = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: batch_update.url(args, options),
    method: 'get',
})

batch_update.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/update/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
batch_update.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return batch_update.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
batch_update.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: batch_update.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::batch_update
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:205
* @route '/corporation/recruitment/update/{character_id}'
*/
batch_update.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: batch_update.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
export const application = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: application.url(args, options),
    method: 'get',
})

application.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/application/{application_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
application.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return application.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
application.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: application.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:96
* @route '/corporation/recruitment/application/{application_id}'
*/
application.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: application.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
export const acl = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acl.url(options),
    method: 'get',
})

acl.definition = {
    methods: ["get","head"],
    url: '/acl/acl',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
acl.url = (options?: RouteQueryOptions) => {
    return acl.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
acl.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acl.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/ListControlGroupsController.php:41
* @route '/acl/acl'
*/
acl.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: acl.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
export const manual_location = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manual_location.url(args, options),
    method: 'get',
})

manual_location.definition = {
    methods: ["get","head"],
    url: '/shared/location/{location_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
manual_location.url = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { location_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            location_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        location_id: args.location_id,
    }

    return manual_location.definition.url
            .replace('{location_id}', parsedArgs.location_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
manual_location.get = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manual_location.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::manual_location
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
manual_location.head = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manual_location.url(args, options),
    method: 'head',
})

const get = {
    batch_status: Object.assign(batch_status, batch_status),
    manuel_locations: Object.assign(manuel_locations, manuel_locations),
    character: Object.assign(character, character),
    mail: Object.assign(mail, mail14724b),
    corporation: Object.assign(corporation, corporation),
    batch_update: Object.assign(batch_update, batch_update),
    application: Object.assign(application, application),
    activity: Object.assign(activity, activity),
    acl: Object.assign(acl, acl),
    affiliated: Object.assign(affiliated, affiliated),
    manual_location: Object.assign(manual_location, manual_location),
    resource: Object.assign(resource, resource),
    markets: Object.assign(markets, markets),
}

export default get