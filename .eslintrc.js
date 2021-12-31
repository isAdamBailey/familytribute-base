module.exports = {
    env: {
        node: true,
    },
    extends: [
        "eslint:recommended",
        "plugin:vue/vue3-recommended",
        "plugin:prettier/recommended",
    ],
    rules: {
        "vue/require-default-prop": "off",
        "vue/no-v-html": "off",
        "vue/multi-word-component-names": "off",
        "no-undef": "off",
    },
};
