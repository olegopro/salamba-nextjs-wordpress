import React, { useState } from 'react'
import Footer from './footer'
import Header from './header'
import { MemoizeMain } from './main'
import Sidebar from './sidebar'
import Head from 'next/head'
import Seo from '../seo'
import { isEmpty } from 'lodash'
import { sanitize } from '../../utils/miscellaneous'
import PropTypes from 'prop-types'

export const MenuToggle = React.createContext(null)

const Layout = ({ data, isPost, children }) => {
	const [isMenuVisible, setMenuVisibility] = useState(false)
	const { page, post, header, footer, headerMenus, footerMenus } = data || {}

	if (isEmpty(page) && isEmpty(post)) {
		return null
	}

	const seo = isPost ? post?.seo ?? {} : page?.seo ?? {}
	const uri = isPost ? post?.uri ?? {} : page?.uri ?? {}

	return (
		<>
			<Seo seo={seo} uri={uri} />
			<Head>
				<link rel="shortcut icon" href={header?.favicon} />
				{seo?.schemaDetails ? (
					<script
						type="application/ld+json"
						className="yoast-schema-graph"
						key="yoastSchema"
						dangerouslySetInnerHTML={{ __html: sanitize(seo?.schemaDetails) }}
					/>
				) : null}
			</Head>
			<MenuToggle.Provider value={{ isMenuVisible, setMenuVisibility }}>
				<Header header={header} />

				<div className="mx-auto ">
					<div className="max-w-screen-xl mx-auto flex justify-between pt-9">
						<MemoizeMain children={children} />
						<Sidebar headerMenus={headerMenus?.edges} />
					</div>
				</div>
			</MenuToggle.Provider>
			<Footer footer={footer} footerMenus={footerMenus?.edges} />
		</>
	)
}

Layout.propTypes = {
	data: PropTypes.object,
	isPost: PropTypes.bool,
	children: PropTypes.object
}

Layout.defaultProps = {
	data: {},
	isPost: false,
	children: {}
}

export default Layout
