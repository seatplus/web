import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/mails',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::getMail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
export const getMail = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMail.url(args, options),
    method: 'get',
})

getMail.definition = {
    methods: ["get","head"],
    url: '/character/mails/content/{mail_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::getMail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
getMail.url = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMail.definition.url
            .replace('{mail_id}', parsedArgs.mail_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::getMail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
getMail.get = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMail.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::getMail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:70
* @route '/character/mails/content/{mail_id}'
*/
getMail.head = (args: { mail_id: string | number } | [mail_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMail.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mailHeaders
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
export const mailHeaders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mailHeaders.url(options),
    method: 'get',
})

mailHeaders.definition = {
    methods: ["get","head"],
    url: '/character/mails/headers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mailHeaders
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
mailHeaders.url = (options?: RouteQueryOptions) => {
    return mailHeaders.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mailHeaders
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
mailHeaders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mailHeaders.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mailHeaders
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
mailHeaders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mailHeaders.url(options),
    method: 'head',
})

const MailsController = { index, getMail, mailHeaders }

export default MailsController