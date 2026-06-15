import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
const QueueController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: QueueController.url(options),
    method: 'get',
})

QueueController.definition = {
    methods: ["get","head"],
    url: '/queue/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
QueueController.url = (options?: RouteQueryOptions) => {
    return QueueController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
QueueController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: QueueController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Queue\QueueController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Queue/QueueController.php:36
* @route '/queue/status'
*/
QueueController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: QueueController.url(options),
    method: 'head',
})

export default QueueController