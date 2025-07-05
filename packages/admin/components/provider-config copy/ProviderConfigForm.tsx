import { useState, useEffect } from '@wordpress/element';
import {
	SelectControl,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import type { Provider } from '@/admin/types/provider';
import {
	PROVIDER_FORM_CONFIGS,
	type FieldConfig,
} from '@/admin/components/provider-config/constants/field-configs';

interface ProviderConfigFormProps {
	provider: Provider;
	onConfigChange?: ( config: Record< string, unknown > ) => void;
}

export const ProviderConfigForm = ( {
	provider,
	onConfigChange,
}: ProviderConfigFormProps ) => {
	const [ formData, setFormData ] = useState< Record< string, unknown > >(
		provider.config || {}
	);

	// Map provider type to config key
	const getConfigKey = ( providerType: string ) => {
		switch ( providerType ) {
			case 'api-key':
				return 'apiKey';
			default:
				return providerType;
		}
	};

	const configKey = getConfigKey( provider.type );
	const fieldConfigs =
		PROVIDER_FORM_CONFIGS[
			configKey as keyof typeof PROVIDER_FORM_CONFIGS
		];

	// Initialize form data with default values
	useEffect( () => {
		if ( ! fieldConfigs ) {
			return;
		}

		const initialData = { ...( provider.config || {} ) };
		Object.entries( fieldConfigs ).forEach( ( [ key, config ] ) => {
			if (
				initialData[ key ] === undefined &&
				config.defaultValue !== undefined
			) {
				initialData[ key ] = config.defaultValue;
			}
		} );

		setFormData( initialData );
	}, [ provider.type, provider.config, fieldConfigs ] );

	// Notify parent of changes
	useEffect( () => {
		onConfigChange?.( formData );
	}, [ formData, onConfigChange ] );

	const handleFieldChange = ( fieldName: string, value: unknown ) => {
		setFormData( ( prev ) => ( {
			...prev,
			[ fieldName ]: value,
		} ) );
	};

	const renderField = ( fieldName: string, config: FieldConfig ) => {
		const value = formData[ fieldName ] || '';
		const stringValue = typeof value === 'string' ? value : '';
		const booleanValue = typeof value === 'boolean' ? value : false;

		const baseProps = {
			key: fieldName,
			label: config.label,
			help: config.help,
		};

		switch ( config.type ) {
			case 'text':
				return (
					<TextControl
						{ ...baseProps }
						type={ config.type }
						value={ stringValue }
						placeholder={ config.placeholder }
						onChange={ ( newValue ) =>
							handleFieldChange( fieldName, newValue )
						}
					/>
				);

			case 'password':
				return (
					<TextControl
						{ ...baseProps }
						type="password"
						value={ stringValue }
						placeholder={ config.placeholder }
						onChange={ ( newValue ) =>
							handleFieldChange( fieldName, newValue )
						}
					/>
				);

			case 'textarea':
				return (
					<TextareaControl
						{ ...baseProps }
						value={ stringValue }
						placeholder={ config.placeholder }
						onChange={ ( newValue ) =>
							handleFieldChange( fieldName, newValue )
						}
						style={ {
							fontFamily: 'monospace',
							fontSize: '12px',
							minHeight: '100px',
						} }
					/>
				);

			case 'select':
				return (
					<SelectControl
						{ ...baseProps }
						value={ stringValue }
						options={ config.options || [] }
						onChange={ ( newValue ) =>
							handleFieldChange( fieldName, newValue )
						}
					/>
				);

			case 'toggle':
				return (
					<ToggleControl
						{ ...baseProps }
						checked={ booleanValue }
						onChange={ ( newValue ) =>
							handleFieldChange( fieldName, newValue )
						}
					/>
				);

			default:
				return null;
		}
	};

	if ( ! fieldConfigs ) {
		return (
			<div
				style={ {
					padding: '20px',
					textAlign: 'center',
					color: '#666',
				} }
			>
				No configuration options available for this provider type.
			</div>
		);
	}

	return (
		<>
			{ Object.entries( fieldConfigs ).map( ( [ fieldName, config ] ) =>
				renderField( fieldName, config )
			) }
		</>
	);
};
