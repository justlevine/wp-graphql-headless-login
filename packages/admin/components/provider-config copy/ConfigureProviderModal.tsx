import { useState } from '@wordpress/element';
import {
	Button,
	Flex,
	Modal,
	PanelBody,
	TextControl,
} from '@wordpress/components';
import type {
	ConfigureProviderModalProps,
	Provider,
} from '@/admin/types/provider';
import { PROVIDER_TYPES } from '@/admin/components/provider-config/constants/providers';
import { ProviderConfigForm } from './ProviderConfigForm';

export const ConfigureProviderModal = ( {
	isOpen,
	provider,
	onClose,
	onSave,
}: ConfigureProviderModalProps ) => {
	const [ providerName, setProviderName ] = useState( provider?.name || '' );
	const [ providerConfig, setProviderConfig ] = useState<
		Record< string, unknown >
	>( provider?.config || {} );

	if ( ! isOpen || ! provider ) {
		return null;
	}

	const providerTypeLabel =
		PROVIDER_TYPES.find( ( t ) => t.value === provider.type )?.label ||
		provider.type;

	const handleSave = () => {
		const updatedProvider: Provider = {
			...provider,
			name: providerName,
			config: providerConfig,
		};

		onSave( updatedProvider );
		onClose();
	};

	const handleConfigChange = ( config: Record< string, unknown > ) => {
		setProviderConfig( config );
	};

	return (
		<Modal
			title={ `Configure ${ provider.name }` }
			onRequestClose={ onClose }
			style={ { minWidth: '500px' } }
		>
			<PanelBody>
				<div style={ { marginBottom: '16px' } }>
					<strong>Provider Type:</strong> { providerTypeLabel }
				</div>

				<TextControl
					label="Provider Name"
					value={ providerName }
					onChange={ setProviderName }
					help="Change the display name for this provider"
				/>

				<ProviderConfigForm
					provider={ provider }
					onConfigChange={ handleConfigChange }
				/>

				<Flex justify="flex-end" style={ { marginTop: 24, gap: 8 } }>
					<Button onClick={ onClose }>Cancel</Button>
					<Button variant="primary" onClick={ handleSave }>
						Save Configuration
					</Button>
				</Flex>
			</PanelBody>
		</Modal>
	);
};
