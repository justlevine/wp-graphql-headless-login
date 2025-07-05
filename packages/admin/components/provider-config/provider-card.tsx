import { Button, Card, CardBody, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { settings, trash, menu } from '@wordpress/icons';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import type { Provider } from '@/admin/types/provider';

import {
	PROVIDER_ICONS,
	PROVIDER_TYPES,
} from '@/admin/components/provider-config/constants/providers';

interface SortableProviderProps {
	provider: Provider;
	onToggleEnabled: ( id: string ) => void;
	onDelete: ( id: string ) => void;
	onConfigure: ( provider: Provider ) => void;
}

const ProviderIcon = ( { providerType }: { providerType: string } ) => {
	const providerIcon = PROVIDER_ICONS[ providerType ] || PROVIDER_ICONS.oauth;

	return (
		<div
			style={ {
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center',
				width: '40px',
				height: '40px',
				backgroundColor: '#f6f7f7',
				borderRadius: '8px',
				color: '#666',
			} }
		>
			<Icon icon={ providerIcon } size={ 20 } />
		</div>
	);
};

const ProviderInfo = ( {
	name,
	type,
	isEnabled,
}: {
	name: string;
	type: string;
	isEnabled: boolean;
} ) => {
	const enabledLabel = isEnabled
		? __( 'Active', 'wp-graphql-headless-login' )
		: __( 'Disabled', 'wp-graphql-headless-login' );

	const enabledColor = isEnabled ? '#059669' : '#dc2626';

	return (
		<div>
			<div
				style={ {
					fontSize: '16px',
					fontWeight: '600',
					color: '#1e1e1e',
					lineHeight: '1.3',
					marginBottom: '2px',
				} }
			>
				{ name }
			</div>
			<div
				style={ {
					fontSize: '13px',
					color: '#6c757d',
					lineHeight: '1.3',
				} }
			>
				{ type }
				<span style={ { margin: '0 8px' } }>•</span>
				<span
					style={ {
						color: enabledColor,
					} }
				>
					{ enabledLabel }
				</span>
			</div>
		</div>
	);
};

const ActionButtons = ( {
	provider,
	onConfigure,
	onDelete,
}: {
	provider: Provider;
	onConfigure: ( provider: Provider ) => void;
	onDelete: ( id: string ) => void;
} ) => (
	<div
		style={ {
			display: 'flex',
			alignItems: 'center',
			gap: '4px',
		} }
	>
		<Button
			icon={ settings }
			variant="secondary"
			size="small"
			onClick={ () => onConfigure( provider ) }
			style={ {
				height: '36px',
				minWidth: '36px',
				borderRadius: '6px',
			} }
		>
			Configure
		</Button>
		<Button
			icon={ trash }
			variant="secondary"
			isDestructive
			size="small"
			onClick={ () => onDelete( provider.id ) }
			style={ {
				height: '36px',
				minWidth: '36px',
				borderRadius: '6px',
			} }
		/>
	</div>
);

export const ProviderCard = ( {
	provider,
	onDelete,
	onConfigure,
}: SortableProviderProps ) => {
	const {
		attributes,
		listeners,
		setNodeRef,
		transform,
		transition,
		isDragging,
	} = useSortable( { id: provider.id } );

	const style = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.5 : 1,
	};

	const providerTypeLabel =
		PROVIDER_TYPES.find( ( t ) => t.value === provider.type )?.label ||
		provider.type;

	return (
		<Card
			ref={ setNodeRef }
			style={ {
				marginBottom: '12px',
				border: '1px solid #e0e0e0',
				borderRadius: '8px',
				backgroundColor: '#fff',
				boxShadow: '0 1px 3px rgba(0, 0, 0, 0.1)',
				...style,
			} }
		>
			<CardBody style={ { padding: '20px 24px' } }>
				<div
					style={ {
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'space-between',
					} }
				>
					{ /* Drag handle */ }
					<div
						{ ...attributes }
						{ ...listeners }
						style={ {
							display: 'flex',
							alignItems: 'center',
							cursor: isDragging ? 'grabbing' : 'grab',
							color: '#999',
							padding: '4px',
						} }
					>
						<Icon icon={ menu } size={ 18 } />
					</div>

					<div
						style={ {
							display: 'flex',
							alignItems: 'flex-start',
							gap: '12px',
							alignSelf: 'stretch',
							flexGrow: 1,
						} }
					>
						<ProviderIcon providerType={ provider.type } />
						<ProviderInfo
							name={ provider.name }
							type={ providerTypeLabel }
							isEnabled={ provider.enabled }
						/>
					</div>

					<ActionButtons
						provider={ provider }
						onConfigure={ onConfigure }
						onDelete={ onDelete }
					/>
				</div>
			</CardBody>
		</Card>
	);
};
