module.exports = {
    testEnvironment: "jsdom",
    moduleFileExtensions: ["js", "json", "vue"],
    transform: {
        "^.+\\.vue$": "@vue/vue3-jest",
        "^.+\\.js$": "babel-jest",
    },
    testMatch: ["**/tests/**/*.test.js", "**/__tests__/**/*.js"],
    collectCoverageFrom: ["resources/js/**/*.{js,vue}", "!**/node_modules/**"],
    moduleNameMapper: {
        "^@/(.*)$": "<rootDir>/resources/js/$1",
    },
};
