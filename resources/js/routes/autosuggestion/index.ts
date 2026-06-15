import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::search
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
export const search = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/shared/autosuggest/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::search
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::search
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::search
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:84
* @route '/shared/autosuggest/search'
*/
search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
export const typesOrGroupOrCategories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: typesOrGroupOrCategories.url(options),
    method: 'get',
})

typesOrGroupOrCategories.definition = {
    methods: ["get","head"],
    url: '/shared/autosuggest/typesOrGroupOrCategories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupOrCategories.url = (options?: RouteQueryOptions) => {
    return typesOrGroupOrCategories.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupOrCategories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: typesOrGroupOrCategories.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::typesOrGroupOrCategories
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:100
* @route '/shared/autosuggest/typesOrGroupOrCategories'
*/
typesOrGroupOrCategories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: typesOrGroupOrCategories.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
export const token = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: token.url(options),
    method: 'get',
})

token.definition = {
    methods: ["get","head"],
    url: '/shared/esi-search/token',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.url = (options?: RouteQueryOptions) => {
    return token.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: token.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\HelperController::token
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/HelperController.php:77
* @route '/shared/esi-search/token'
*/
token.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: token.url(options),
    method: 'head',
})

const autosuggestion = {
    search: Object.assign(search, search),
    typesOrGroupOrCategories: Object.assign(typesOrGroupOrCategories, typesOrGroupOrCategories),
    token: Object.assign(token, token),
}

export default autosuggestion