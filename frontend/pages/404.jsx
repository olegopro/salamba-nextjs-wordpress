import Link from 'next/link'
import client from '../src/apollo/client'
import Header from '../src/components/layout/header'
import Footer from '../src/components/layout/footer'
import { GET_MENUS } from '../src/queries/get-menus'
import { MenuToggle } from '../src/components/layout'
import { MemoizeMain } from '../src/components/layout/main'
import Sidebar from '../src/components/layout/sidebar'
import { useState } from 'react'

function Error404({ data }) {
	const { header, footer, headerMenus, footerMenus } = data || {}
	const [isMenuVisible, setMenuVisibility] = useState(false)

	return (
		<>
			<MenuToggle.Provider value={{ isMenuVisible, setMenuVisibility }}>
				<Header header={header} />

				<div className="mx-auto ">
					<div className="max-w-screen-xl mx-auto flex justify-between pt-9">
						<div>Ошибка 404</div>
						<Sidebar headerMenus={headerMenus?.edges} />
					</div>
				</div>
			</MenuToggle.Provider>
			<Footer footer={footer} footerMenus={footerMenus?.edges} />
		</>
	)
}

export default Error404

export async function getStaticProps() {
	const { data } = await client.query({
		query: GET_MENUS
	})

	return {
		props: {
			data: data || {}
		}
	}
}
