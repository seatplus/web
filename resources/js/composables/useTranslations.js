import { inject } from 'vue';
import I18n from '@/vendor/I18n';

export const I18nKey = Symbol('i18n');

/**
 * Translation helper for `<script setup>` / Composition-API components.
 *
 * Mirrors the Options-API `this.$I18n` global (kept for un-migrated pages) but
 * works inside `setup()`. Falls back to a fresh I18n instance when used outside
 * the provided app tree (e.g. isolated component tests), since I18n is stateless
 * beyond the `window.translations` key it reads.
 */
export function useTranslations() {
    const i18n = inject(I18nKey, null) ?? new I18n();

    return {
        trans: (key, replace = {}) => i18n.trans(key, replace),
        trans_choice: (key, count = 1, replace = {}) => i18n.trans_choice(key, count, replace),
    };
}
