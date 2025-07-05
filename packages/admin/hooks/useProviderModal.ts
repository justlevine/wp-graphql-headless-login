import { useState, useCallback } from '@wordpress/element';
import type { Provider } from '@/admin/types/provider';

export interface UseProviderModalReturn {
	isConfigureModalOpen: boolean;
	configuringProvider: Provider | null;
	openConfigureModal: ( provider: Provider ) => void;
	closeConfigureModal: () => void;
	isAddModalOpen: boolean;
	openAddModal: () => void;
	closeAddModal: () => void;
}

export const useProviderModal = (): UseProviderModalReturn => {
	const [ isConfigureModalOpen, setConfigureModalOpen ] = useState( false );
	const [ configuringProvider, setConfiguringProvider ] =
		useState< Provider | null >( null );
	const [ isAddModalOpen, setAddModalOpen ] = useState( false );

	const openConfigureModal = useCallback( ( provider: Provider ) => {
		setConfiguringProvider( provider );
		setConfigureModalOpen( true );
	}, [] );

	const closeConfigureModal = useCallback( () => {
		setConfiguringProvider( null );
		setConfigureModalOpen( false );
	}, [] );

	const openAddModal = useCallback( () => {
		setAddModalOpen( true );
	}, [] );

	const closeAddModal = useCallback( () => {
		setAddModalOpen( false );
	}, [] );

	return {
		isConfigureModalOpen,
		configuringProvider,
		openConfigureModal,
		closeConfigureModal,
		isAddModalOpen,
		openAddModal,
		closeAddModal,
	};
};
