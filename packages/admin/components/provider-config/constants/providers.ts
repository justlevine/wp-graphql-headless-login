// eslint-disable-next-line @typescript-eslint/no-explicit-any
import { lock, atSymbol, globe, key } from '@wordpress/icons';
import type { ProviderType } from '@/admin/types/provider';

export const PROVIDER_TYPES: ProviderType[] = [
	{ value: 'oauth', label: 'OAuth 2.0' },
	{ value: 'saml', label: 'SAML' },
	{ value: 'ldap', label: 'LDAP' },
	{ value: 'email', label: 'Email/Password' },
	{ value: 'api-key', label: 'API Key' },
];

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const PROVIDER_ICONS: Record< string, any > = {
	oauth: lock,
	saml: lock,
	ldap: globe,
	email: atSymbol,
	'api-key': key,
};

export const MOCK_PROVIDERS = [
	{
		id: '1',
		name: 'GitHub OAuth',
		type: 'oauth',
		enabled: true,
		order: 1,
	},
	{
		id: '2',
		name: 'Google OAuth',
		type: 'oauth',
		enabled: true,
		order: 2,
	},
	{
		id: '3',
		name: 'Corporate SAML',
		type: 'saml',
		enabled: false,
		order: 3,
	},
	{
		id: '4',
		name: 'Email Login',
		type: 'email',
		enabled: true,
		order: 4,
	},
	{
		id: '5',
		name: 'Internal LDAP',
		type: 'ldap',
		enabled: false,
		order: 5,
	},
];
