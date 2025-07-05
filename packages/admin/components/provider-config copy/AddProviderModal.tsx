import React, { useState, ComponentType } from 'react';
import { __ } from '@wordpress/i18n';
import { Modal, PanelBody, TextControl } from '@wordpress/components';
import type { ProviderType } from '@/admin/types/provider';
import type { ConfigureProviderModalProps } from './provider-config/provider-screen';

interface AddProviderModalWithConfigureProps {
	isOpen: boolean;
	onClose: () => void;
	onAdd: ( provider: ProviderType ) => void;
	providerTypes: ProviderType[];
	configureModal: ComponentType< ConfigureProviderModalProps > | undefined;
}

export const AddProviderModal: React.FC< AddProviderModalWithConfigureProps > = ( {
	isOpen,
	onClose,
	onAdd,
	providerTypes,
	configureModal,
} ) => {
	const [ search, setSearch ] = useState( '' );
	const [ selectedType, setSelectedType ] = useState< ProviderType | null >( null );
	const [ showConfigure, setShowConfigure ] = useState( false );

	if ( ! isOpen ) {
		return null;
	}

	const filteredTypes = providerTypes.filter( ( type ) =>
		type.label.toLowerCase().includes( search.toLowerCase() )
	);

	// Defensive: If configureModal is not provided, show an error message
	if ( showConfigure && selectedType ) {
		if ( ! configureModal ) {
			return (
				<Modal
					title={ __( 'Error', 'wp-graphql-headless-login' ) }
					onRequestClose={ () => {
						setShowConfigure( false );
						setSelectedType( null );
						onClose();
					} }
				>
					<PanelBody>
						<p style={ { color: 'red' } }>
							{ __(
								'Configuration modal is not available. Please contact support.',
								'wp-graphql-headless-login'
							) }
						</p>
					</PanelBody>
				</Modal>
			);
		}
		const ConfigureModal = configureModal;
		return (
			<ConfigureModal
				isOpen={ true }
				provider={ selectedType }
				onClose={ () => {
					setShowConfigure( false );
					setSelectedType( null );
					onClose();
				} }
				onSave={ onAdd }
				mode="create"
			/>
		);
	}

	return (
		<Modal
			title={ __( 'Select Provider Type', 'wp-graphql-headless-login' ) }
			onRequestClose={ onClose }
		>
			<PanelBody>
				<TextControl
					value={ search }
					onChange={ setSearch }
					placeholder={ __(
						'Search provider types…',
						'wp-graphql-headless-login'
					) }
				/>
				<ul style={ { marginTop: 16, padding: 0, listStyle: 'none' } }>
					{ filteredTypes.map( ( type ) => (
						<li key={ type.value } style={ { marginBottom: 8 } }>
							<button
								style={ {
									width: '100%',
									display: 'flex',
									alignItems: 'center',
									justifyContent: 'space-between',
									padding: '12px 16px',
									border: '1px solid #e0e0e0',
									borderRadius: 8,
									background: '#fff',
									cursor: 'pointer',
									boxShadow: '0 1px 2px rgba(0,0,0,0.03)',
									transition: 'box-shadow 0.15s',
									fontSize: 16,
								} }
								onClick={ () => {
								setSelectedType( type );
								setShowConfigure( true );
							} }
								aria-label={ type.label }
							>
								<span
									style={ {
										display: 'flex',
										alignItems: 'center',
										gap: 12,
									} }
								>
									{ type.icon && (
										<span
											style={ {
												display: 'flex',
												alignItems: 'center',
												fontSize: 20,
											} }
										>
											{ type.icon }
										</span>
									) }
									<span>{ type.label }</span>
								</span>
								<span
									aria-hidden="true"
									style={ {
										color: '#bbb',
										fontSize: 20,
										marginLeft: 8,
									} }
								>
									&#8250;
								</span>
							</button>
						</li>
					) ) }
					{ filteredTypes.length === 0 && (
						<li style={ { color: '#888', padding: 8 } }>
							{ __(
								'No provider types found.',
								'wp-graphql-headless-login'
							) }
						</li>
					) }
				</ul>
			</PanelBody>
		</Modal>
	);
};
