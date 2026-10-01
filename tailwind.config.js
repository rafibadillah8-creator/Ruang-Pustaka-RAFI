/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                serif: ['"Fraunces"', 'serif'],
            },
            colors: {
                // Charcoal gelap ala perpustakaan tua
                ink: {
                    900: '#2e2a24',
                },
                // Cokelat kayu kenari (walnut) - warna utama, ganti "wine" sebelumnya
                wine: {
                    50:  '#f4efec',
                    100: '#e6d9d2',
                    200: '#cbb3a5',
                    300: '#ab8672',
                    400: '#7c5445',
                    500: '#5a382c',
                    600: '#472c22',
                    700: '#3e2723',
                    800: '#2f1d1a',
                    900: '#221512',
                },
                // Kuningan/brass tua sebagai aksen (voucher, badge)
                gold: {
                    400: '#b08d57',
                    500: '#8c6d3f',
                },
                // Krem perkamen sebagai warna latar
                parchment: '#f5f5dc',
            },
            boxShadow: {
                card: '0 1px 2px rgba(46,42,36,0.08), 0 8px 24px -12px rgba(46,42,36,0.22)',
            },
        },
    },
    plugins: [],
};