import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/onboarding',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::index
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:15
* @route '/onboarding'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::complete
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:33
* @route '/onboarding'
*/
export const complete = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/onboarding',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::complete
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:33
* @route '/onboarding'
*/
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Onboarding\OnboardingController::complete
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Onboarding/OnboardingController.php:33
* @route '/onboarding'
*/
complete.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

const OnboardingController = { index, complete }

export default OnboardingController