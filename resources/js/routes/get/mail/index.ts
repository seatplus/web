import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::headers
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
export const headers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: headers.url(options),
    method: 'get',
})

headers.definition = {
    methods: ["get","head"],
    url: '/character/mails/headers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::headers
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
headers.url = (options?: RouteQueryOptions) => {
    return headers.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::headers
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
headers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: headers.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::headers
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:55
* @route '/character/mails/headers'
*/
headers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: headers.url(options),
    method: 'head',
})

const mail = {
    headers: Object.assign(headers, headers),
}

export default mail