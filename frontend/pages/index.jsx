import { getIconComponentByName } from '../src/utils/icons-map'

export default function Home() {
	return (
		<div className=" bg-gray-300 drop mx-auto h-screen">
			<header className="bg-gray-100 shadow-md">
				<div className="flex h-24 max-w-screen-xl items-center justify-between container  bg-gray-100 mx-auto">
					<div className="flex items-center">
						<a href="/">{getIconComponentByName('logo')}</a>
						<h3 className="ml-6 uppercase text-lg leading-6 font-medium text-gray-900">Главная страница</h3>
					</div>

					<input
						className="h-10 w-64 pl-4 outline-none bg-gray-200 placeholder-blue-300 font-bold text-sm"
						type="text"
						placeholder="Поиск..."
					/>
				</div>
			</header>
			<main>тело</main>
		</div>
	)
}
