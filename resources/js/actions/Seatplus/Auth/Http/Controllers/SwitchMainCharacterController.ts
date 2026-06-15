import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Seatplus\Auth\Http\Controllers\SwitchMainCharacterController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/SwitchMainCharacterController.php:35
* @route '/auth/main-character/switch/{new_character_id}'
*/
const SwitchMainCharacterController = (args: { new_character_id: string | number } | [new_character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: SwitchMainCharacterController.url(args, options),
    method: 'put',
})

SwitchMainCharacterController.definition = {
    methods: ["put"],
    url: '/auth/main-character/switch/{new_character_id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \Seatplus\Auth\Http\Controllers\SwitchMainCharacterController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/SwitchMainCharacterController.php:35
* @route '/auth/main-character/switch/{new_character_id}'
*/
SwitchMainCharacterController.url = (args: { new_character_id: string | number } | [new_character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { new_character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            new_character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        new_character_id: args.new_character_id,
    }

    return SwitchMainCharacterController.definition.url
            .replace('{new_character_id}', parsedArgs.new_character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\SwitchMainCharacterController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/SwitchMainCharacterController.php:35
* @route '/auth/main-character/switch/{new_character_id}'
*/
SwitchMainCharacterController.put = (args: { new_character_id: string | number } | [new_character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: SwitchMainCharacterController.url(args, options),
    method: 'put',
})

export default SwitchMainCharacterController