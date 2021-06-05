import Footer from './footer'
import Header from './header'
import Main from './main'
import Sidebar from './sidebar'

const Layout = ({ data, children }) => {
	return (
		<>
			<Header />

			<div className=" mx-auto h-screen">
				<div className=" max-w-screen-xl mx-auto flex justify-between  pt-9">
					<Main />
					<Sidebar headerMenus={data?.menus?.headerMenus} />
				</div>
			</div>

			{children}
			<Footer />
		</>
	)
}

export default Layout
