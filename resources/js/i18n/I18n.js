/**
 * Pure translation formatter: lookup + placeholder replacement + pluralization over a
 * translations object (the current-locale `translations` Inertia prop). Stateless — the
 * caller passes the bag, so it stays reactive to locale/page changes.
 *
 * Structure it reads (matches the server-side Translations::gather output):
 *   { 'web::': { auth: { login_welcome: '…' } }, validation: { required: '…' } }
 */
export default class I18n
{
    /**
     * Get and replace the string of the given key.
     *
     * @param  {object}  translations
     * @param  {string}  key
     * @param  {object}  replace
     * @return {string}
     */
    static trans(translations, key, replace = {})
    {
        return I18n._replace(I18n._extract(translations, key), replace);
    }

    /**
     * Get and pluralize the strings of the given key.
     *
     * @param  {object}  translations
     * @param  {string}  key
     * @param  {number}  count
     * @param  {object}  replace
     * @return {string}
     */
    static trans_choice(translations, key, count = 1, replace = {})
    {
        let lines = I18n._extract(translations, key, '|').split('|'), translation;

        lines.some(t => translation = I18n._match(t, count));

        translation = translation || (count > 1 ? lines[1] : lines[0]);

        translation = translation.replace(/\[.*?\]|\{.*?\}/, '');

        return I18n._replace(translation, replace);
    }

    /**
     * Merge translation bags (e.g. the shared baseline + a page's own groups) at the
     * namespace level, combining their group sub-objects.
     *
     * @param  {...object}  bags
     * @return {object}
     */
    static merge(...bags)
    {
        let merged = {};

        for (let bag of bags) {
            for (let namespace in (bag || {})) {
                merged[namespace] = { ...(merged[namespace] || {}), ...bag[namespace] };
            }
        }

        return merged;
    }

    /**
     * Match the translation limit with the count.
     *
     * @param  {string}  translation
     * @param  {number}  count
     * @return {string|null}
     */
    static _match(translation, count)
    {
        let match = translation.match(/^[{[]([^[\]{}]*)[}\]](.*)/);

        if (! match) return;

        if (match[1].includes(',')) {
            let [from, to] = match[1].split(',', 2);

            if (to === '*' && count >= from) {
                return match[2];
            } else if (from === '*' && count <= to) {
                return match[2];
            } else if (count >= from && count <= to) {
                return match[2];
            }
        }

        return match[1] == count ? match[2] : null;
    }

    /**
     * Replace the placeholders.
     *
     * @param  {string}  translation
     * @param  {object}  replace
     * @return {string}
     */
    static _replace(translation, replace)
    {
        if (typeof translation === 'object') {
            return translation;
        }

        for (let placeholder in replace) {
            translation = translation.toString()
                .replace(`:${placeholder}`, replace[placeholder])
                .replace(`:${placeholder.toUpperCase()}`, replace[placeholder].toString().toUpperCase())
                .replace(
                    `:${placeholder.charAt(0).toUpperCase()}${placeholder.slice(1)}`,
                    replace[placeholder].toString().charAt(0).toUpperCase() + replace[placeholder].toString().slice(1)
                );
        }

        return translation.toString().trim()
    }

    /**
     * Extract values from the translations object by (namespaced) dot notation.
     *
     * @param  {object}  translations
     * @param  {string}  key
     * @param  {mixed}  value
     * @return {mixed}
     */
    static _extract(translations, key, value = null)
    {
        let path = key.toString().split('::'),
            keys = path.pop().toString().split('.');

        if (path.length > 0) {
            path[0] += '::';
        }

        return path.concat(keys).reduce((t, i) => (t && t[i]) || (value || key), translations || {});
    }
}
