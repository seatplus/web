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
];
