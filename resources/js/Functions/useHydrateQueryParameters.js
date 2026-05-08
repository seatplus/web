
export function useHydrateQueryParameters(params = {}) {

    const queryParameters = Object.fromEntries(new URLSearchParams(window.location.search))

    return _.merge(params, queryParameters)

}