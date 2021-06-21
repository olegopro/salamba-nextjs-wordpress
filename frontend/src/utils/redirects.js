import { isEmpty } from 'lodash'

export const getPreviewRedirectUrl = (postType = '', previewPostId = '') => {
	if (isEmpty(postType) || isEmpty(previewPostId)) {
		return ''
	}

	//если пришло НЕ пустое значение то проверяем - страница или новость
	switch (postType) {
		case 'post':
			return `/blog/preview/${previewPostId}/`
		case 'page':
			return `/page/preview/${previewPostId}/`
		default:
			return '/'
	}
}

export const getLoginPreviewRedirectUrl = (postType = '', previewPostId = '') => {
	return `/login/?postType=${postType || ''}&previewPostId=${previewPostId || ''}`
}
