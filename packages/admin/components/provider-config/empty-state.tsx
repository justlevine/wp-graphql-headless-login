import { Button, Card, CardBody, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { plus, lock } from '@wordpress/icons';

export const EmptyState = ( {
	onAddProvider,
}: {
	onAddProvider: () => void;
} ) => {
	const noProvidersConfiguredMessage = __(
		'No providers configured',
		'wp-graphql-headless-login'
	);
	const addFirstProviderMessage = __(
		'Add your first authentication provider to get started.',
		'wp-graphql-headless-login'
	);
	const buttonLabel = __( 'Add Provider', 'wp-graphql-headless-login' );

	return (
		<div style={ { maxWidth: '100%', margin: '0', padding: '20px' } }>
			<Card
				style={ {
					border: '2px dashed #e0e0e0',
					borderRadius: '8px',
					textAlign: 'center',
					padding: '60px 20px',
				} }
			>
				<CardBody>
					<Icon
						icon={ lock }
						size={ 48 }
						style={ { color: '#999', marginBottom: '16px' } }
					/>
					<h2
						style={ {
							fontSize: '18px',
							fontWeight: '500',
							margin: '0 0 8px 0',
							color: '#23282d',
						} }
					>
						{ noProvidersConfiguredMessage }
					</h2>
					<p
						style={ {
							margin: '0 0 24px 0',
							color: '#6c757d',
							fontSize: '14px',
						} }
					>
						{ addFirstProviderMessage }
					</p>
					<Button
						icon={ plus }
						variant="primary"
						onClick={ onAddProvider }
						style={ {
							height: '40px',
							paddingLeft: '16px',
							paddingRight: '16px',
						} }
					>
						{ buttonLabel }
					</Button>
				</CardBody>
			</Card>
		</div>
	);
};
