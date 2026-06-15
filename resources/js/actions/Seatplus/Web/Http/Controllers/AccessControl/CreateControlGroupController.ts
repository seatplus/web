import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
const CreateControlGroupController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: CreateControlGroupController.url(options),
    method: 'post',
})

CreateControlGroupController.definition = {
    methods: ["post"],
    url: '/acl/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
CreateControlGroupController.url = (options?: RouteQueryOptions) => {
    return CreateControlGroupController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/AccessControl/CreateControlGroupController.php:13
* @route '/acl/create'
*/
CreateControlGroupController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: CreateControlGroupController.url(options),
    method: 'post',
})

export default CreateControlGroupController