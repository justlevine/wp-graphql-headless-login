import {
	DndContext,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
	DragEndEvent,
} from '@dnd-kit/core';
import {
	arrayMove,
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import type { Provider } from '@/admin/types/provider';

export interface UseDragAndDropReturn {
	sensors: ReturnType< typeof useSensors >;
	dndContext: typeof DndContext;
	sortableContext: typeof SortableContext;
	verticalListSortingStrategy: typeof verticalListSortingStrategy;
	handleDragEnd: (
		event: DragEndEvent,
		providers: Provider[],
		onReorder: ( providers: Provider[] ) => void
	) => void;
}

export const useDragAndDrop = (): UseDragAndDropReturn => {
	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const handleDragEnd = (
		event: DragEndEvent,
		providers: Provider[],
		onReorder: ( providers: Provider[] ) => void
	) => {
		const { active, over } = event;

		if ( ! over ) {
			return;
		}

		if ( active.id !== over.id ) {
			const oldIndex = providers.findIndex( ( p ) => p.id === active.id );
			const newIndex = providers.findIndex( ( p ) => p.id === over.id );

			const newProviders = arrayMove( providers, oldIndex, newIndex );
			onReorder( newProviders );
		}
	};

	return {
		sensors,
		dndContext: DndContext,
		sortableContext: SortableContext,
		verticalListSortingStrategy,
		handleDragEnd,
	};
};
