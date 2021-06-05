import { getIconComponentByName } from '../../../utils/icons-map'

const Header = () => {
	return (
		<header className="bg-gray-50 shadow-lg ">
			<div className="flex h-24 max-w-screen-xl items-center justify-between container bg-gray-50 mx-auto">
				<div className="flex items-center">
					<a href="/">{getIconComponentByName('logo')}</a>
					<h3 className="ml-6 uppercase text-lg leading-6 font-medium text-gray-900">Главная страница</h3>
				</div>

				<input
					className="h-10 w-64 pl-4 outline-none bg-gray-100 placeholder-blue-300 font-bold text-sm"
					type="text"
					placeholder="Поиск..."
				/>
			</div>
		</header>
	)
}

export default Header
