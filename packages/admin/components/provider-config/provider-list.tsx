import { closestCenter } from '@dnd-kit/core';
import { useDragAndDrop } from '@/admin/hooks/use-drag-and-drop';
import { ProviderCard } from './provider-card';
import type { Provider } from '@/admin/types/provider';

export const ProviderList = ( {
	providers,
	onReorder,
	onToggleEnabled,
	onDelete,
	onConfigure,
}: {
	providers: Provider[];
	onReorder: ( providers: Provider[] ) => void;
	onToggleEnabled: ( id: string ) => void;
	onDelete: ( id: string ) => void;
	onConfigure: ( provider: Provider ) => void;
} ) => {
	const {
		sensors,
		dndContext: DndContext,
		sortableContext: SortableContext,
		verticalListSortingStrategy,
		handleDragEnd,
	} = useDragAndDrop();

	return (
		<DndContext
			sensors={ sensors }
			collisionDetection={ closestCenter }
			onDragEnd={ ( event ) =>
				handleDragEnd( event, providers, onReorder )
			}
		>
			<SortableContext
				items={ providers.map( ( p ) => p.id ) }
				strategy={ verticalListSortingStrategy }
			>
				{ providers.map( ( provider ) => (
					<ProviderCard
						key={ provider.id }
						provider={ provider }
						onToggleEnabled={ onToggleEnabled }
						onDelete={ onDelete }
						onConfigure={ onConfigure }
					/>
				) ) }
			</SortableContext>
		</DndContext>
	);
};
