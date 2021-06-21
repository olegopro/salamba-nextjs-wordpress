import { loginUser } from '../../src/utils/api'
import cookie from 'cookie'

export default async function login(req, res) {
	const { username, password } = req?.body ?? {}
	const data = await loginUser({ username, password })

	/**
	 * Обратите внимание, что когда вы запускаете npm run start локально, файлы cookie не будут установлены,
	 * потому что локально process.env.NODE_ENV = 'production' «secure» будет истинным,
	 * но при локальном тестировании он все равно будет http, а не https.
	 * Поэтому при локальном тестировании как в dev, так и в prod,
	 * установите для значения «secure» значение false.
	 */
	res.setHeader(
		'Set-Cookie',
		cookie.serialize('auth', String(data?.login?.authToken ?? ''), {
			httpOnly: true,
			secure: 'development' !== process.env.NODE_ENV,
			path: '/',
			maxAge: 60 * 60 * 24 * 7 // 1 week
		})
	)

	// Отправка только успешного сообщения, потому что мы не хотим отправлять JWT клиенту.
	res.status(200).json({ success: Boolean(data?.login?.authToken) })
}
