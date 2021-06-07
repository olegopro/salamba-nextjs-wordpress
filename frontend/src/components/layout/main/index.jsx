import React from 'react'

const Main = () => {
	return <main className="col-span-3">Главный экран{console.log('render')}</main>
}

export const MemoizeMain = React.memo(Main)
