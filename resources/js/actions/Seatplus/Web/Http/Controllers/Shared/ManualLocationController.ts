import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/configuration/manual_locations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:45
* @route '/configuration/manual_locations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getSuggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
export const getSuggestions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuggestions.url(options),
    method: 'get',
})

getSuggestions.definition = {
    methods: ["get","head"],
    url: '/configuration/manual_locations/suggestions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getSuggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
getSuggestions.url = (options?: RouteQueryOptions) => {
    return getSuggestions.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getSuggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
getSuggestions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuggestions.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getSuggestions
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:54
* @route '/configuration/manual_locations/suggestions'
*/
getSuggestions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSuggestions.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::acceptSuggestion
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
export const acceptSuggestion = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acceptSuggestion.url(options),
    method: 'post',
})

acceptSuggestion.definition = {
    methods: ["post"],
    url: '/configuration/manual_locations/suggestions',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::acceptSuggestion
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
acceptSuggestion.url = (options?: RouteQueryOptions) => {
    return acceptSuggestion.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::acceptSuggestion
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:70
* @route '/configuration/manual_locations/suggestions'
*/
acceptSuggestion.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acceptSuggestion.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getLocation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
export const getLocation = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLocation.url(args, options),
    method: 'get',
})

getLocation.definition = {
    methods: ["get","head"],
    url: '/shared/location/{location_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getLocation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
getLocation.url = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { location_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            location_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        location_id: args.location_id,
    }

    return getLocation.definition.url
            .replace('{location_id}', parsedArgs.location_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getLocation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
getLocation.get = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLocation.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::getLocation
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:93
* @route '/shared/location/{location_id}'
*/
getLocation.head = (args: { location_id: string | number } | [location_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLocation.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/shared/location',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\ManualLocationController::create
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/ManualLocationController.php:120
* @route '/shared/location'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

const ManualLocationController = { index, getSuggestions, acceptSuggestion, getLocation, create }

export default ManualLocationController