import { Button } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import { useProviders } from '@/admin/hooks/useProviders';
import { useProviderModal } from '@/admin/hooks/useProviderModal';
import { PROVIDER_TYPES } from '@/admin/components/provider-config/constants/providers';
import {
	AddProviderModal,
	ConfigureProviderModal,
} from '../provider-config copy';
import { ScreenHeader } from '../screen/screen-header';
import { ProviderList } from './provider-list';
import { EmptyState } from './empty-state';
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

	// If no providers, show empty state
	if ( totalCount === 0 ) {
		return (
			<>
				<ScreenHeader
					title={ wpGraphQLLogin?.settings?.providers?.title }
					description={
						wpGraphQLLogin?.settings?.providers?.description
					}
				/>
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
				title={ wpGraphQLLogin?.settings?.providers?.title }
				description={ wpGraphQLLogin?.settings?.providers?.description }
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
			<Summary totalCount={ totalCount } activeCount={ activeCount } />

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
