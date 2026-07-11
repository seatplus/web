// Minimal axios-free HTTP helpers built on the native fetch API.
//
// The installed Inertia client (@inertiajs/vue3 v2) has no `useHttp` hook, so for
// the handful of non-Inertia JSON endpoints (batch dispatch/status, id resolving,
// …) we talk to the backend directly. Laravel's VerifyCsrfToken accepts the
// `X-XSRF-TOKEN` header carrying the (url-decoded) `XSRF-TOKEN` cookie — exactly
// what axios used to send for us.

function xsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

async function request(url, { method = 'GET', body } = {}) {
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
        headers['X-XSRF-TOKEN'] = xsrfToken();
    }

    const response = await fetch(url, {
        method,
        headers,
        credentials: 'same-origin',
        body: body !== undefined ? JSON.stringify(body) : undefined,
    });

    if (!response.ok) {
        throw new Error(`Request to ${url} failed with status ${response.status}`);
    }

    return response;
}

export async function getJson(url) {
    return (await request(url)).json();
}

export function post(url, body = {}) {
    return request(url, { method: 'POST', body });
}
