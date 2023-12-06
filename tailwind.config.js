/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./public/assets/admin/**/*.js",
        "./templates/Admin/**/*.html.twig"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
}
