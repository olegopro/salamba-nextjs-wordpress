import React, { useState } from 'react'
import Footer from './footer'
import Header from './header'
import { MemoizeMain } from './main'
import Sidebar from './sidebar'
import Head from 'next/head'
import Seo from '../seo'
import { isEmpty } from 'lodash'
import { sanitize } from '../../utils/miscellaneous'

export const MenuToggle = React.createContext(null)

const Layout = ({ data, children }) => {
	const [isMenuVisible, setMenuVisibility] = useState(false)

	console.log(data)

	if (isEmpty(data?.page)) return null

	const { page, header, footer, menus } = data || {}

	return (
		<>
			<Seo seo={page?.seo} uri={page?.uri} />
			<Head>
				<link rel="shortcut icon" href={data?.header?.favicon} />
				{page?.seo?.schemaDetails && (
					<script
						type="application/ld+json"
						className="yoast-schema-graph"
						key="yoastSchema"
						dangerouslySetInnerHTML={{ __html: sanitize(page?.seo?.schemaDetails) }}
					/>
				)}
			</Head>
			<MenuToggle.Provider value={{ isMenuVisible, setMenuVisibility }}>
				<Header header={data?.header} />

				<div className="mx-auto ">
					<div className="max-w-screen-xl mx-auto flex justify-between pt-9">
						<MemoizeMain />
						<Sidebar headerMenus={data?.menus?.headerMenus} />
					</div>
				</div>
			</MenuToggle.Provider>
			{children}
			<Footer footer={data?.footer} footerMenus={data?.menus?.footerMenus} />
		</>
	)
}

export default Layout
