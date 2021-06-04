module.exports = {
	purge: ['./src/components/**/*.{js,jsx,ts,tsx}', './pages/**/*.{js,jsx,ts,tsx}'],
	theme: {
		extend: {}
	},
	variants: {},
	plugins: [require('tailwindcss'), require('precss'), require('autoprefixer')]
}
