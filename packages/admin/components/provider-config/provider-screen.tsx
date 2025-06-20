import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import { useProviders } from '@/admin/hooks/useProviders';
import { useProviderModal } from '@/admin/hooks/useProviderModal';
import { PROVIDER_TYPES } from '@/admin/components/provider-config/constants/providers';
import {
	AddProviderModal,
	ConfigureProviderModal,
	EmptyState,
	ProviderList,
} from '../provider-config copy';
import { ScreenHeader } from '../screen/screen-header';
import { Summary } from './summary';

export const ProviderScreen = () => {
	const {
		providers,
		addProvider,
		updateProvider,
		deleteProvider,
		toggleEnabled,
		reorderProviders,
		totalCount,
		activeCount,
	} = useProviders();

	const {
		isConfigureModalOpen,
		configuringProvider,
		openConfigureModal,
		closeConfigureModal,
		isAddModalOpen,
		openAddModal,
		closeAddModal,
	} = useProviderModal();

	const title = __( 'Login Providers', 'wp-graphql-headless-login' );
	const description = __(
		'Configure the Authentication Providers that are available to users.',
		'wp-graphql-headless-login'
	);

	// If no providers, show empty state
	if ( totalCount === 0 ) {
		return (
			<>
				<ScreenHeader title={ title } description={ description } />
				<EmptyState onAddProvider={ openAddModal } />
				<AddProviderModal
					isOpen={ isAddModalOpen }
					onClose={ closeAddModal }
					onAdd={ addProvider }
					providerTypes={ PROVIDER_TYPES }
				/>
			</>
		);
	}

	const AddProviderButton = () => (
		<Button
			icon={ plus }
			variant="primary"
			onClick={ openAddModal }
			style={ {
				height: '40px',
				paddingLeft: '16px',
				paddingRight: '16px',
			} }
		>
			Add Provider
		</Button>
	);

	return (
		<>
			<ScreenHeader
				title={ title }
				description={ description }
				actions={ <AddProviderButton /> }
			/>
			{ /* Provider List */ }
			<ProviderList
				providers={ providers }
				onReorder={ reorderProviders }
				onToggleEnabled={ toggleEnabled }
				onDelete={ deleteProvider }
				onConfigure={ openConfigureModal }
			/>

			{ /* Summary */ }
			<Summary
				totalCount={ totalCount }
				activeCount={ activeCount }
				isActiveFilter={ false }
				onToggleFilter={ ( filter ) => {
					// Implement filter logic if needed
					// For now, just log the filter state
					console.log( 'Filter active providers:', filter );
				} }
			/>

			{ /* Modals */ }
			<AddProviderModal
				isOpen={ isAddModalOpen }
				onClose={ closeAddModal }
				onAdd={ addProvider }
				providerTypes={ PROVIDER_TYPES }
			/>

			<ConfigureProviderModal
				isOpen={ isConfigureModalOpen }
				provider={ configuringProvider }
				onClose={ closeConfigureModal }
				onSave={ ( provider ) =>
					updateProvider( provider.id, provider )
				}
			/>
		</>
	);
};
