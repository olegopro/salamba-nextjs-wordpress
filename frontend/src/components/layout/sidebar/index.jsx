import { isEmpty } from 'lodash'
import Navigation from './navigation'

const Sidebar = ({ headerMenus }) => {
	if (isEmpty(headerMenus)) {
		return null
	}

	return (
		<aside>
			<Navigation headerMenus={headerMenus} />
		</aside>
	)
}

export default Sidebar
