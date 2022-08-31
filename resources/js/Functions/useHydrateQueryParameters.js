//import route from 'ziggy'

export function useHydrateQueryParameters(params = {}) {


    const queryParameters = route().params

    return _.merge(params, queryParameters, )

}