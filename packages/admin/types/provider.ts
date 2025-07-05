export interface Provider {
	id: string;
	name: string;
	type: string;
	enabled: boolean;
	order: number;
	config?: Record< string, unknown >;
}

export interface ProviderType {
	value: string;
	label: string;
	icon?: React.ReactNode;
}

export interface AddProviderModalProps {
	isOpen: boolean;
	onClose: () => void;
	onAdd: ( provider: Omit< Provider, 'id' | 'order' > ) => void;
	providerTypes: ProviderType[];
}

export interface ConfigureProviderModalProps {
	isOpen: boolean;
	provider: Provider | null;
	onClose: () => void;
	onSave: ( provider: Provider ) => void;
}
