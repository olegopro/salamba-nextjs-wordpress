import { useContext, useState } from 'react'
import { MenuToggle } from '..'
import { getIconComponentByName } from '../../../utils/icons-map'

const Header = () => {
	const { isMenuVisible, setMenuVisibility } = useContext(MenuToggle)

	return (
		<header className="bg-gray-50 shadow-lg ">
			<div className="flex h-24 max-w-screen-xl items-center justify-between container bg-gray-50 mx-auto">
				<div className="flex flex-grow items-center">
					<a href="/">{getIconComponentByName('logo')}</a>
					<h3 className=" ml-6 uppercase text-lg leading-6 font-medium text-gray-900">Главная страница</h3>
				</div>

				<input
					className="h-10 w-64 pl-4 mr-4 lg:mr-0 outline-none bg-gray-100 placeholder-blue-300 font-bold text-sm"
					type="text"
					placeholder="Поиск..."
				/>
				<button onClick={() => setMenuVisibility(!isMenuVisible)} className="lg:hidden mr-5 focus:outline-none">
					{getIconComponentByName('navIcon')}
				</button>
			</div>
		</header>
	)
}

export default Header
