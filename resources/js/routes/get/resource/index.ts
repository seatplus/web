import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::variants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
export const variants = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: variants.url(args, options),
    method: 'get',
})

variants.definition = {
    methods: ["get","head"],
    url: '/shared/image/variants/{resource_type}/{resource_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::variants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
variants.url = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            resource_type: args[0],
            resource_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        resource_type: args.resource_type,
        resource_id: args.resource_id,
    }

    return variants.definition.url
            .replace('{resource_type}', parsedArgs.resource_type.toString())
            .replace('{resource_id}', parsedArgs.resource_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::variants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
variants.get = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: variants.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::variants
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:148
* @route '/shared/image/variants/{resource_type}/{resource_id}'
*/
variants.head = (args: { resource_type: string | number, resource_id: string | number } | [resource_type: string | number, resource_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: variants.url(args, options),
    method: 'head',
})

const resource = {
    variants: Object.assign(variants, variants),
}

export default resource