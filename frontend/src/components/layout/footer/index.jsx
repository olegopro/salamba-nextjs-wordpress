import { isArray, isEmpty } from 'lodash'
import Link from 'next/link'
import { getIconComponentByName } from '../../../utils/icons-map'

const Footer = ({ footer, footerMenus }) => {
	if (isEmpty(footerMenus) || !isArray(footerMenus)) {
		return null
	}

	return (
		<footer>
			<div className="bg-gray-600 ">
				<div className="flex flex-wrap mx-auto max-w-screen-xl">
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
			</div>

			<div className=" bg-gray-300  ">
				<div className="flex flex-wrap mx-auto justify-center max-w-screen-xl">
					<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">Популярное за неделю</div>
					<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">Самое обсуждаемое</div>
					<div className="my-1 px-1 w-full sm:w-full lg:w-1/2 xl:w-1/3">В тренде</div>
				</div>
			</div>

			<div className="bg-gray-700">
				<div className="flex  mx-auto  max-w-screen-xl items-center">
					<div className="text-white my-1 px-1 w-full  xl:w-1/2">
						{footer?.copyrightText ? copyrightText : 'Шаблон заполнения текста в подвале'}
					</div>

					<div className=" my-3 px-1 w-full xl:w-1/2 flex justify-end">
						{console.log(footer.socialLinks)}
						{!isEmpty(footer?.socialLinks) && isArray(footer?.socialLinks) ? (
							<ul className="flex items-center ">
								{footer?.socialLinks.map(socialLink => (
									<li className="ml-4" key={socialLink?.iconName}>
										<a href={socialLink?.iconUrl}>{getIconComponentByName(socialLink?.iconName)}</a>
									</li>
								))}
							</ul>
						) : null}
					</div>
				</div>
			</div>
		</footer>
	)
}

export default Footer
