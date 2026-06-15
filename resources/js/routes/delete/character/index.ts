import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
export const application = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: application.url(args, options),
    method: 'delete',
})

application.definition = {
    methods: ["delete"],
    url: '/corporation/recruitment/application/character/{character_id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
application.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return application.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController::application
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ApplicationsController.php:60
* @route '/corporation/recruitment/application/character/{character_id}'
*/
application.delete = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: application.url(args, options),
    method: 'delete',
})

const character = {
    application: Object.assign(application, application),
}

export default character