import { usePage } from '@inertiajs/vue3';
import I18n from '@/i18n/I18n';

/**
 * Translation helper for `<script setup>` / Composition-API components.
 *
 * Reads the current page's `translations` Inertia prop (shared chrome baseline + the
 * groups the page's controller declared via SharesTranslations), so lookups are reactive
 * to locale and page changes.
 */
export function useTranslations() {
    const page = usePage();
    const bag = () => page.props.translations || {};

    return {
        trans: (key, replace = {}) => I18n.trans(bag(), key, replace),
        trans_choice: (key, count = 1, replace = {}) => I18n.trans_choice(bag(), key, count, replace),
    };
}
