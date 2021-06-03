module.exports = {
	purge: ['./src/components/**/*.js', './pages/**/*.js', './src/components/**/*.jsx', './pages/**/*.jsx'],
	theme: {
		extend: {}
	},
	variants: {},
	plugins: [require('tailwindcss'), require('precss'), require('autoprefixer')]
}
