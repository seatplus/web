import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
export const detail = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: detail.url(args, options),
    method: 'post',
})

detail.definition = {
    methods: ["post"],
    url: '/character/contacts/{character_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
detail.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return detail.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::detail
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:61
* @route '/character/contacts/{character_id}'
*/
detail.post = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: detail.url(args, options),
    method: 'post',
})

const contacts = {
    detail: Object.assign(detail, detail),
}

export default contacts