import { Panel } from '@wordpress/components';
import clsx from 'clsx';
import { Suspense, type PropsWithChildren } from 'react';
import { Loading } from '@/admin/components/ui/loading';
import { useCurrentScreen } from './context';
import { SettingsScreen } from './setting-screen';
import { ProviderScreen } from '@/admin/components/provider-config/provider-screen';

import styles from './styles.module.scss';
import { getSettingForScreen } from './utils';
import { __ } from '@wordpress/i18n';

const Wrapper = ( {
	children,
	className,
}: PropsWithChildren< {
	className?: string;
} > ) => {
	const classes = clsx( styles.wrapper, className );

	return <Panel className={ classes }>{ children }</Panel>;
};

const ComposedSettingsScreen = ( {
	currentScreen,
}: {
	currentScreen: string;
} ) => {
	const settingKey = getSettingForScreen( currentScreen );

	return <SettingsScreen settingKey={ settingKey } />;
};

export const Screen = () => {
	const { currentScreen } = useCurrentScreen();

	return (
		<Suspense fallback={ <Loading /> }>
			<Wrapper>
				{ currentScreen === 'providers' ? (
					<ProviderScreen />
				) : (
					<ComposedSettingsScreen currentScreen={ currentScreen } />
				) }
			</Wrapper>
		</Suspense>
	);
};
