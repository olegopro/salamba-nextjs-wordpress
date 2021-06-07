import { isArray, isEmpty } from 'lodash'
import Link from 'next/link'

const Footer = ({ footer, footerMenus }) => {
	if (isEmpty(footerMenus) || !isArray(footerMenus)) {
		return null
	}

	console.log(footerMenus)

	return (
		<footer>
			<div className="flex flex-wrap bg-gray-600 -mx-1 overflow-hidden">
				<div className="my-1 px-1 w-full overflow-hidden sm:w-full lg:w-1/2 xl:w-1/3">
					<div className="text-white" dangerouslySetInnerHTML={{ __html: footer?.sidebarOne }}></div>
				</div>
				<div className="my-1 px-1 w-full overflow-hidden sm:w-full lg:w-1/2 xl:w-1/3">
					<div>
						<div className="text-white" dangerouslySetInnerHTML={{ __html: footer?.sidebarTwo }}></div>
					</div>
				</div>
				<div className="my-1 px-1 text-white w-full overflow-hidden sm:w-full lg:w-1/2 xl:w-1/3">
					{!isEmpty(footerMenus) && isArray(footerMenus) ? (
						<ul>
							{footerMenus.map(footerMenu => (
								<li key={footerMenu?.node?.id}>
									{
										<Link href={footerMenu?.node?.path}>
											<a>{footerMenu?.node?.label}</a>
										</Link>
									}
								</li>
							))}
						</ul>
					) : null}
				</div>
			</div>

			<div className="flex flex-wrap bg-gray-300 -mx-1 ">
				<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">Популярное за неделю</div>
				<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">Самое обсуждаемое</div>
				<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">В тренде</div>
			</div>

			<div className="flex flex-wrap -mx-1 ">
				<div className="my-1 px-1 w-full  xl:w-1/2">
					{footer?.copyrightText ? copyrightText : 'Шаблон заполнения текста в подвале'}
				</div>

				<div className="my-1 px-1 w-full xl:w-1/2"></div>
			</div>
		</footer>
	)
}

export default Footer
