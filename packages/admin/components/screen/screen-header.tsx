import clsx from 'clsx';
import { ReactNode } from 'react';

import styles from './styles.module.scss';

export const ScreenHeader = ( {
	title,
	description,
	className,
	actions,
}: {
	title: string;
	description?: string;
	className?: string;
	actions?: ReactNode;
} ) => {
	return (
		<div className={ clsx( styles?.header, className ) }>
			<div>
				<h2 className="components-panel__body-title">{ title }</h2>
				{ description && (
					<p dangerouslySetInnerHTML={ { __html: description } } />
				) }
			</div>
			{ /* Actions can be buttons or other elements */ }
			{ actions && <>{ actions }</> }
		</div>
	);
};
