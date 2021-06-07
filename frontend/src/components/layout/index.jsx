import React, { useState } from 'react'
import Footer from './footer'
import Header from './header'
import { MemoizeMain } from './main'
import Sidebar from './sidebar'

export const MenuToggle = React.createContext(null)

const Layout = ({ data, children }) => {
	const [isMenuVisible, setMenuVisibility] = useState(false)

	return (
		<>
			<MenuToggle.Provider value={{ isMenuVisible, setMenuVisibility }}>
				<Header />

				<div className="mx-auto h-screen">
					<div className="max-w-screen-xl mx-auto flex justify-between pt-9">
						<MemoizeMain />
						<Sidebar headerMenus={data?.menus?.headerMenus} />
					</div>
				</div>
			</MenuToggle.Provider>

			{children}
			<Footer />
		</>
	)
}

export default Layout
