// @ts-nocheck
import client from '../src/apollo/client'
import Layout from '../src/components/layout'
import { GET_MENUS } from '../src/queries/get-menus'

export default function Home({ data }) {
	return <Layout data={data} />
}

export async function getStaticProps(context) {
	const { data, loading, networkStatus } = await client.query({ query: GET_MENUS })

	return {
		props: {
			data: {
				header: data?.header || [],
				menus: {
					headerMenus: data?.headerMenus?.edges || [],
					footerMenus: data?.footerMenus?.edges || []
				},
				footer: data?.footer || []
			}
		},
		revalidate: 1
	}
}
