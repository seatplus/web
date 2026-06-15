import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LoginController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LoginController.php:12
* @route '/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Auth\LogoutController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Auth/LogoutController.php:11
* @route '/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\HomeController::home
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/HomeController.php:40
* @route '/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::onboarding
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
export const onboarding = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: onboarding.url(options),
    method: 'get',
})

onboarding.definition = {
    methods: ["get","head"],
    url: '/onboarding',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::onboarding
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
onboarding.url = (options?: RouteQueryOptions) => {
    return onboarding.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::onboarding
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
onboarding.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: onboarding.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::onboarding
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
onboarding.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: onboarding.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
export const enable_esi_search = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enable_esi_search.url(options),
    method: 'get',
})

enable_esi_search.definition = {
    methods: ["get","head"],
    url: '/shared/esi-search/enable_esi_search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
enable_esi_search.url = (options?: RouteQueryOptions) => {
    return enable_esi_search.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
enable_esi_search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enable_esi_search.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Shared\EnableEsiSearchController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Shared/EnableEsiSearchController.php:13
* @route '/shared/esi-search/enable_esi_search'
*/
enable_esi_search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enable_esi_search.url(options),
    method: 'head',
})

