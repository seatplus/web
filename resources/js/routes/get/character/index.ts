import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import assets from './assets'
import skill from './skill'
/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:51
* @route '/character/skills/{character_id}/skills'
*/
export const skills = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skills.url(args, options),
    method: 'get',
})

skills.definition = {
    methods: ["get","head"],
    url: '/character/skills/{character_id}/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:51
* @route '/character/skills/{character_id}/skills'
*/
skills.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return skills.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:51
* @route '/character/skills/{character_id}/skills'
*/
skills.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skills.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:51
* @route '/character/skills/{character_id}/skills'
*/
skills.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: skills.url(args, options),
    method: 'head',
})

const character = {
    assets: Object.assign(assets, assets),
    skills: Object.assign(skills, skills),
    skill: Object.assign(skill, skill),
}

export default character