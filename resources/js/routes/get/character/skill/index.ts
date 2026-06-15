import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::queue
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:59
* @route '/character/skills/{character_id}/skillqueue'
*/
export const queue = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: queue.url(args, options),
    method: 'get',
})

queue.definition = {
    methods: ["get","head"],
    url: '/character/skills/{character_id}/skillqueue',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::queue
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:59
* @route '/character/skills/{character_id}/skillqueue'
*/
queue.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return queue.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::queue
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:59
* @route '/character/skills/{character_id}/skillqueue'
*/
queue.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: queue.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::queue
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:59
* @route '/character/skills/{character_id}/skillqueue'
*/
queue.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: queue.url(args, options),
    method: 'head',
})

const skill = {
    queue: Object.assign(queue, queue),
}

export default skill