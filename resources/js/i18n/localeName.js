/**
 * Native display name for a locale code (e.g. 'de' → 'Deutsch'), via the browser's
 * Intl.DisplayNames — no server-side map needed. Falls back to the code if unsupported.
 *
 * @param  {string}  code
 * @return {string}
 */
export function localeName(code) {
    try {
        return new Intl.DisplayNames([code], { type: 'language' }).of(code) ?? code;
    } catch (e) {
        return code;
    }
}
