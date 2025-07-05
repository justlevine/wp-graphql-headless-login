import { useState, useCallback } from '@wordpress/element';
import type { Provider } from '@/admin/types/provider';
import { MOCK_PROVIDERS } from '@/admin/components/provider-config/constants/providers';

export const useProviders = (
	initialProviders: Provider[] = MOCK_PROVIDERS
) => {
	const [ providers, setProviders ] =
		useState< Provider[] >( initialProviders );

	const addProvider = useCallback(
		( newProvider: Omit< Provider, 'id' | 'order' > ) => {
			setProviders( ( prev ) => [
				...prev,
				{
					...newProvider,
					id: Date.now().toString(),
					order: prev.length + 1,
				},
			] );
		},
		[]
	);

	const updateProvider = useCallback(
		( id: string, updates: Partial< Provider > ) => {
			setProviders( ( prev ) =>
				prev.map( ( provider ) =>
					provider.id === id ? { ...provider, ...updates } : provider
				)
			);
		},
		[]
	);

	const deleteProvider = useCallback( ( id: string ) => {
		setProviders( ( prev ) => {
			const filtered = prev.filter( ( p ) => p.id !== id );
			// Reorder remaining providers
			return filtered.map( ( provider, index ) => ( {
				...provider,
				order: index + 1,
			} ) );
		} );
	}, [] );

	const toggleEnabled = useCallback(
		( id: string ) => {
			updateProvider( id, {
				enabled: ! providers.find( ( p ) => p.id === id )?.enabled,
			} );
		},
		[ providers, updateProvider ]
	);

	const reorderProviders = useCallback(
		( reorderedProviders: Provider[] ) => {
			// Update order property to match new positions
			const providersWithOrder = reorderedProviders.map(
				( provider, index ) => ( {
					...provider,
					order: index + 1,
				} )
			);
			setProviders( providersWithOrder );
		},
		[]
	);

	const getProviderById = useCallback(
		( id: string ) => {
			return providers.find( ( p ) => p.id === id );
		},
		[ providers ]
	);

	const getActiveCount = useCallback( () => {
		return providers.filter( ( p ) => p.enabled ).length;
	}, [ providers ] );

	const getSortedProviders = useCallback( () => {
		return [ ...providers ].sort( ( a, b ) => a.order - b.order );
	}, [ providers ] );

	return {
		providers: getSortedProviders(),
		addProvider,
		updateProvider,
		deleteProvider,
		toggleEnabled,
		reorderProviders,
		getProviderById,
		totalCount: providers.length,
		activeCount: getActiveCount(),
	};
};
