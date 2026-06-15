import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
const GetRecruitmentIndexController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetRecruitmentIndexController.url(options),
    method: 'get',
})

GetRecruitmentIndexController.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
GetRecruitmentIndexController.url = (options?: RouteQueryOptions) => {
    return GetRecruitmentIndexController.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
GetRecruitmentIndexController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GetRecruitmentIndexController.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
GetRecruitmentIndexController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GetRecruitmentIndexController.url(options),
    method: 'head',
})

export default GetRecruitmentIndexController