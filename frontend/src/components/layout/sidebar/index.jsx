import { isEmpty } from 'lodash'
import { useContext } from 'react'
import { MenuToggle } from '..'

import Navigation from './navigation'

const Sidebar = ({ headerMenus }) => {
	const { isMenuVisible } = useContext(MenuToggle)

	if (isEmpty(headerMenus)) {
		return null
	}

	return (
		<aside className={`${isMenuVisible ? '' : 'hidden lg:block'}`}>
			<Navigation headerMenus={headerMenus} />
		</aside>
	)
}

export default Sidebar
