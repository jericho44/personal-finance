import js from "@eslint/js";
import globals from "globals";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";
import { defineConfig } from "eslint/config";
import vueParser from "vue-eslint-parser";


export default defineConfig([
    {
        files: ["**/*.{js,mjs,cjs,ts,mts,cts,vue}"],
        plugins: {
            js,
            vue: pluginVue,
            "@typescript-eslint": tseslint.plugin,
        },
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                parser: tseslint.parser,
                ecmaVersion: "latest",
                sourceType: "module",
                ecmaFeatures: {
                    jsx: false,
                },
            },
            globals: {
                ...globals.browser,
                ...globals.node,
                $: 'readonly',
                ImportMetaEnv: 'readonly',
            },
        },
        rules: {
            // General rules
            "no-console": "warn",
            "no-debugger": "error",
            "no-undef": "error",
            "no-func-assign": "error",
            "no-extra-semi": "warn",
            "prefer-const": "warn",
            "no-useless-catch": "warn",
            "no-const-assign": "error",
            "no-dupe-args": "error",
            "no-redeclare": "error",
            "no-dupe-keys": "error",
            "no-import-assign": "error",
            "no-unreachable": "warn",
            "no-unsafe-negation": "error",
            "no-var": "error",

            //   "@typescript-eslint/naming-convention": [
            //     "warn",
            //     { selector: "variableLike", format: ["camelCase", "PascalCase"] },
            //     { selector: "function", format: ["camelCase", "PascalCase"] },
            //     { selector: "interface", format: ["PascalCase"] },
            //   ],
            "@typescript-eslint/no-unused-vars": "warn",
            "@typescript-eslint/no-explicit-any": "warn",
            //   "@typescript-eslint/ban-types": [
            //     "error",
            //     {
            //       types: {
            //         String: { message: "Gunakan 'string' alih-alih 'String'" },
            //         Boolean: { message: "Gunakan 'boolean' alih-alih 'Boolean'" },
            //         Number: { message: "Gunakan 'number' alih-alih 'Number'" },
            //         "{}": { message: "Hindari '{}', gunakan 'Record<string, unknown>'" },
            //         Object: { message: "Gunakan 'object' atau 'Record<string, unknown>'" },
            //         Function: { message: "Gunakan tipe fungsi eksplisit, misalnya: '() => void'" },
            //       },
            //       extendDefaults: true,
            //     },
            //   ],
            "@typescript-eslint/no-array-constructor": "error",
            "@typescript-eslint/no-duplicate-enum-values": "error",
            "@typescript-eslint/no-non-null-asserted-optional-chain": "error",
            "@typescript-eslint/no-var-requires": "error",
            "@typescript-eslint/no-redeclare": "error",

            // Vue rules
            "vue/no-mutating-props": "error",
            "vue/no-unused-components": "warn",
            "vue/no-unused-vars": "warn",
            "vue/require-default-prop": "warn",
            "vue/require-prop-types": "error",
            "vue/component-definition-name-casing": ["error", "PascalCase"],
            "vue/no-deprecated-slot-attribute": "error",
            "vue/valid-v-slot": "error",
            "vue/no-async-in-computed-properties": "error",
            "vue/no-computed-properties-in-data": "error",
            "vue/no-dupe-keys": "error",
            "vue/no-dupe-v-else-if": "error",
            "vue/no-duplicate-attributes": "error",
            "vue/no-export-in-script-setup": "error",
            "vue/no-multiple-template-root": "error",
            "vue/no-parsing-error": "error",
            "vue/no-template-key": "error",
            "vue/no-use-v-if-with-v-for": "error",
            "vue/no-useless-template-attributes": "error",
            "vue/no-v-for-template-key-on-child": "error",
            "vue/valid-v-if": "error",
            "vue/html-end-tags": "error",
            "vue/html-self-closing": "error",
        },
    },
]);
