/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#0f172a',
                cloud: '#e2e8f0',
                signal: '#0891b2',
                success: '#15803d',
                warning: '#b45309',
            },
            fontFamily: {
                heading: ['Sora', 'sans-serif'],
                body: ['Public Sans', 'sans-serif'],
            },
            boxShadow: {
                soft: '0 10px 35px -20px rgba(15, 23, 42, 0.45)',
            },
        },
    },
    plugins: [],
};
