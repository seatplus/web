/**
 * Thin fetch() wrapper that mirrors the behaviour of the previous axios setup:
 *  - Reads the XSRF-TOKEN cookie and forwards it as X-XSRF-TOKEN (Laravel CSRF)
 *  - Sets X-Requested-With: XMLHttpRequest so Laravel treats the request as AJAX
 *  - Accepts query params as a plain object (appended to the URL)
 *  - Returns the parsed JSON body directly (like axios `.data`)
 *  - Throws on non-2xx responses (like axios does by default)
 *  - Accepts an AbortSignal for cancellation
 */

function getCsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
    return match ? decodeURIComponent(match[1]) : null
}

/**
 * @param {string} url
 * @param {Object} [options]
 * @param {string} [options.method='GET']
 * @param {Object|null} [options.params] - query string params
 * @param {Object|null} [options.data]   - JSON body (for POST/PUT/PATCH)
 * @param {AbortSignal} [options.signal]
 * @returns {Promise<any>} parsed JSON response body
 */
export async function apiFetch(url, { method = 'GET', params = null, data = null, signal = null } = {}) {
    if (params && Object.keys(params).length) {
        const qs = new URLSearchParams(
            Object.entries(params).filter(([, v]) => v !== null && v !== undefined)
        ).toString()
        url = url + (url.includes('?') ? '&' : '?') + qs
    }

    const headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    }

    const csrf = getCsrfToken()
    if (csrf) headers['X-XSRF-TOKEN'] = csrf

    const fetchOptions = { method, headers }
    if (signal) fetchOptions.signal = signal
    if (data && Object.keys(data).length) {
        headers['Content-Type'] = 'application/json'
        fetchOptions.body = JSON.stringify(data)
    }

    const response = await fetch(url, fetchOptions)

    if (!response.ok) {
        const err = new Error(`HTTP ${response.status}: ${response.statusText}`)
        err.response = response
        throw err
    }

    return response.json()
}
