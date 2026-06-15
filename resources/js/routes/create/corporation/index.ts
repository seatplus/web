import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::recruitment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:40
* @route '/corporation/recruitment'
*/
export const recruitment = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recruitment.url(options),
    method: 'post',
})

recruitment.definition = {
    methods: ["post"],
    url: '/corporation/recruitment',
} satisfies RouteDefinition<["post"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::recruitment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:40
* @route '/corporation/recruitment'
*/
recruitment.url = (options?: RouteQueryOptions) => {
    return recruitment.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController::recruitment
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/EnlistmentsController.php:40
* @route '/corporation/recruitment'
*/
recruitment.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recruitment.url(options),
    method: 'post',
})

const corporation = {
    recruitment: Object.assign(recruitment, recruitment),
}

export default corporation