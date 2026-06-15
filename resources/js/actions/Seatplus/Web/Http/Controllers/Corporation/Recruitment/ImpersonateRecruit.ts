import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
const ImpersonateRecruit = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ImpersonateRecruit.url(args, options),
    method: 'get',
})

ImpersonateRecruit.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment/impersonate/{application_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
ImpersonateRecruit.url = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { application_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            application_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        application_id: args.application_id,
    }

    return ImpersonateRecruit.definition.url
            .replace('{application_id}', parsedArgs.application_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
ImpersonateRecruit.get = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ImpersonateRecruit.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit.php:37
* @route '/corporation/recruitment/impersonate/{application_id}'
*/
ImpersonateRecruit.head = (args: { application_id: string | number } | [application_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ImpersonateRecruit.url(args, options),
    method: 'head',
})

export default ImpersonateRecruit