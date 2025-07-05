import { __, sprintf } from '@wordpress/i18n';
import styles from './styles.module.scss';

export const Summary = ( {
	totalCount,
	activeCount,
}: {
	totalCount: number;
	activeCount: number;
} ) => {
	const singularProvider = __( 'provider', 'wp-graphql-headless-login' );
	const pluralProvider = __( 'providers', 'wp-graphql-headless-login' );

	const message = sprintf(
		/* translators: %1$d is the total count, %2$s is the singular or plural form of provider */
		__(
			'%1$d %2$s configured • %3$d active.',
			'wp-graphql-headless-login'
		),
		totalCount,
		totalCount === 1 ? singularProvider : pluralProvider,
		activeCount
	);

	return (
		<div className={ styles.summary }>
			<p>{ message }</p>
		</div>
	);
};
