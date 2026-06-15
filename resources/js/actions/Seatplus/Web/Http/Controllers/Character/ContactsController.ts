import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/character/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::getContacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
export const getContacts = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getContacts.url(args, options),
    method: 'post',
})

getContacts.definition = {
    methods: ["post"],
    url: '/character/contacts/{character_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::getContacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
getContacts.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getContacts.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::getContacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
getContacts.post = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getContacts.url(args, options),
    method: 'post',
})

const ContactsController = { index, getContacts }

export default ContactsController