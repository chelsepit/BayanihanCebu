import js from "@eslint/js";
import globals from "globals";

export default [
    {
        ignores: ["node_modules", "vendor", "public", "storage"],
    },
    {
        files: ["**/*.js"],
        languageOptions: {
            ecmaVersion: "latest",
            sourceType: "module",
            globals: {
                ...globals.browser,
                ...globals.node, // ✅ add Node.js globals (require, process, module, etc.)
            },
        },
        rules: {
            "no-unused-vars": "warn",
            "no-undef": "error",
            "no-console": "off",
            semi: ["error", "always"],
            quotes: ["error", "double"],
        },
    },
];
