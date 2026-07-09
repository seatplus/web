import { usePage } from '@inertiajs/vue3';
import I18n from '@/i18n/I18n';

/**
 * Translation helper for `<script setup>` / Composition-API components.
 *
 * Merges the shared `translations` baseline with the page's own `pageTranslations`
 * (declared by its controller), so lookups are reactive to locale and page changes.
 */
export function useTranslations() {
    const page = usePage();
    const bag = () => I18n.merge(page.props.translations, page.props.pageTranslations);

    return {
        trans: (key, replace = {}) => I18n.trans(bag(), key, replace),
        trans_choice: (key, count = 1, replace = {}) => I18n.trans_choice(bag(), key, count, replace),
    };
}
