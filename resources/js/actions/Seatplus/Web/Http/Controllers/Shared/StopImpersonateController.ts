import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
const StopImpersonateController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StopImpersonateController.url(options),
    method: 'get',
})

StopImpersonateController.definition = {
    methods: ["get","head"],
    url: '/stop/impersonate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
StopImpersonateController.url = (options?: RouteQueryOptions) => {
    return StopImpersonateController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
StopImpersonateController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StopImpersonateController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\StopImpersonateController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/StopImpersonateController.php:33
* @route '/stop/impersonate'
*/
StopImpersonateController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: StopImpersonateController.url(options),
    method: 'head',
})

export default StopImpersonateController