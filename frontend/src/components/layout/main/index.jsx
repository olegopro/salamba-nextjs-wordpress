import React from 'react'

const Main = ({ children }) => {
	return (
		<main className="col-span-3">
			Главный экран <div>{children}</div>
		</main>
	)
}

export const MemoizeMain = React.memo(Main)
