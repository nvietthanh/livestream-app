module.exports = {
    root: true,
    env: {
        node: true,
        browser: true,
        es6: true,
    },
    parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
    },
    extends: ['eslint:recommended', 'plugin:vue/vue3-recommended', 'prettier'],
    rules: {
        'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'vue/require-default-prop': 0,
        'vue/no-unused-vars': 'error',
        'vue/multi-word-component-names': 'off',
        'vue/singleline-html-element-content-newline': 'off',
        'vue/component-name-in-template-casing': ['error', 'PascalCase'],
        'vue/max-attributes-per-line': 'off',
        indent: ['error', 4],
    },
    overrides: [
        {
            files: ['*.vue'],
            rules: {
                indent: 'off',
            },
        },
    ],
    ignorePatterns: [
        'node_modules/',
        'public/shared',
        '**/node_modules/',
        '/**/node_modules/*',
        '/**/public/vendor/*',
        'out/',
        'dist/',
        'build/',
    ],
    globals: {
        axios: 'readonly',
        axiosPlugin: 'readonly',
        route: 'readonly',
    },
}