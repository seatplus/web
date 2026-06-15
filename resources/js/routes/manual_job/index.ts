import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::entities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
export const entities = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: entities.url(options),
    method: 'post',
})

entities.definition = {
    methods: ["post"],
    url: '/queue/manual_job/entities',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::entities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
entities.url = (options?: RouteQueryOptions) => {
    return entities.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\DispatchJobController::entities
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/DispatchJobController.php:84
* @route '/queue/manual_job/entities'
*/
entities.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: entities.url(options),
    method: 'post',
})

const manual_job = {
    entities: Object.assign(entities, entities),
}

export default manual_job