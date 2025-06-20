import { __ } from '@wordpress/i18n';
import { ToggleControl } from '@wordpress/components';
import styles from './styles.module.scss';

export const Summary = ( {
	totalCount,
	activeCount,
	onToggleFilter,
	isActiveFilter = false,
}: {
	totalCount: number;
	activeCount: number;
	onToggleFilter: ( shouldFilter: boolean ) => void;
	isActiveFilter: boolean;
} ) => {
	const singularProvider = __( 'provider', 'wp-graphql-headless-login' );
	const pluralProvider = __( 'providers', 'wp-graphql-headless-login' );

	const message = isActiveFilter
		? `${ activeCount } active ${
				activeCount === 1 ? singularProvider : pluralProvider
		  } • ${ totalCount } configured.`
		: `${ totalCount } ${
				totalCount === 1 ? singularProvider : pluralProvider
		  } configured • ${ activeCount } active.`;

	const FilterToggle = () => {
		return (
			<ToggleControl
				label={
					isActiveFilter
						? __( 'View all', 'wp-graphql-headless-login' )
						: __( 'Hide inactive', 'wp-graphql-headless-login' )
				}
				onChange={ onToggleFilter }
				checked={ isActiveFilter }
				className={ styles.summaryToggleButton }
			/>
		);
	};

	return (
		<div className={ styles.summary }>
			<p>{ message }</p>
			<FilterToggle />
		</div>
	);
};
