import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
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

const onboarding = {
    complete: Object.assign(complete, complete),
}

export default onboarding