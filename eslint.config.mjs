import globals from "globals";
import pluginJs from "@eslint/js";
import pluginVue from "eslint-plugin-vue";


export default [
    {
        languageOptions: {
            globals: {
                ...globals.browser,
                _: "readonly",
                route: "readonly",
                axios: "readonly",
            },
        }},
    pluginJs.configs.recommended,
    ...pluginVue.configs["flat/recommended"],
    {
        rules: {
            // Page/single-file components here are routinely single-word (Inertia
            // page names, e.g. Members, Applicants) or intentionally named after a
            // domain concept that collides with an HTML element (Link, Time, Toast).
            // Renaming ~80 components + every reference is disproportionate and risky;
            // these two stylistic rules are off for this project.
            "vue/multi-word-component-names": "off",
            "vue/no-reserved-component-names": "off",
        },
    },
];
