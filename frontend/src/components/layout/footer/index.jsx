import { isEmpty } from 'lodash'

const Footer = ({ footer, footerMenus }) => {
	if (isEmpty(footerMenus)) {
		return null
	}
	return <footer className="max-w-screen-xl mx-auto">Подвал</footer>
}

export default Footer
