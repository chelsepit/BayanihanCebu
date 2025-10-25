import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    build: {
        outDir: "public/build", 
        manifest: true,        
        rollupOptions: {
            input: ["resources/css/app.css", "resources/js/app.js"],
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
