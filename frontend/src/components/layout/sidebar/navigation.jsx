import { isEmpty } from 'lodash'
import Link from 'next/link'
import classes from '../../../styles/navigation.module.scss'
import { getIconComponentByName } from '../../../utils/icons-map'

const Navigation = ({ headerMenus }) => {
	if (isEmpty(headerMenus)) {
		return null
	}

	return (
		<>
			<div className="flex items-center w-64 h-16 pl-4 mb-8 shadow-lg font-bold text-lg text-blue-400 bg-white">
				<h3 className="flex items-center">
					<i className="mr-2">{getIconComponentByName('navIcon')}</i>
					Навигация
				</h3>
			</div>
			<nav className="flex items-center w-64 h-auto shadow-lg font-bold text-sm text-blue-400 bg-white">
				{headerMenus.length ? (
					<ul>
						{headerMenus?.map(menu => (
							<li
								key={menu?.node?.id}
								className="main-list-nav flex items-center hover:bg-gray-200 duration-300 ease-in-out"
							>
								<Link key={menu?.node?.id} href={menu?.node?.path}>
									<a className={classes.mainListNav + ' pl-4 flex h-16 w-64 items-center'}>
										{menu?.node?.label}
									</a>
								</Link>
							</li>
						))}
					</ul>
				) : null}
			</nav>
		</>
	)
}

export default Navigation
