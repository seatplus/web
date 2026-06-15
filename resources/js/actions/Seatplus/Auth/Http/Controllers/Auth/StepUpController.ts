import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
const StepUpController = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StepUpController.url(args, options),
    method: 'get',
})

StepUpController.definition = {
    methods: ["get","head"],
    url: '/auth/eve/sso/{character_id}/step_up',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
StepUpController.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return StepUpController.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
StepUpController.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StepUpController.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Auth\Http\Controllers\Auth\StepUpController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/vendor/seatplus/auth/src/Http/Controllers/Auth/StepUpController.php:41
* @route '/auth/eve/sso/{character_id}/step_up'
*/
StepUpController.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: StepUpController.url(args, options),
    method: 'head',
})

export default StepUpController