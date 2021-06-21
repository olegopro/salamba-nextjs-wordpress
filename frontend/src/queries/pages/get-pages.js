import { gql } from '@apollo/client'
// import { PAGE_COUNT } from '../../utils/slug'

export const GET_PAGES_URI = gql`
	query GET_PAGES_URI {
		pages: pages(last: 1) {
			nodes {
				id
				uri
			}
		}
	}
`
