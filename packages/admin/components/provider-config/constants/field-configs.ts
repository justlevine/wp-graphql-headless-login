export interface FieldConfig {
	type: 'text' | 'password' | 'textarea' | 'select' | 'toggle';
	label: string;
	placeholder?: string;
	help?: string;
	options?: Array< { label: string; value: string } >;
	required?: boolean;
	defaultValue?: string | boolean;
}

export interface ProviderFormConfig {
	oauth: Record< string, FieldConfig >;
	saml: Record< string, FieldConfig >;
	ldap: Record< string, FieldConfig >;
	email: Record< string, FieldConfig >;
	apiKey: Record< string, FieldConfig >;
}

export const PROVIDER_FORM_CONFIGS: ProviderFormConfig = {
	oauth: {
		clientId: {
			type: 'text',
			label: 'Client ID',
			placeholder: 'Enter your OAuth client ID',
			help: 'The client ID from your OAuth provider',
			required: true,
		},
		clientSecret: {
			type: 'password',
			label: 'Client Secret',
			placeholder: 'Enter your OAuth client secret',
			help: 'The client secret from your OAuth provider',
			required: true,
		},
		authorizationUrl: {
			type: 'text',
			label: 'Authorization URL',
			placeholder: 'https://provider.com/oauth/authorize',
			help: 'The authorization endpoint URL',
			required: true,
		},
		tokenUrl: {
			type: 'text',
			label: 'Token URL',
			placeholder: 'https://provider.com/oauth/token',
			help: 'The token endpoint URL',
			required: true,
		},
	},
	saml: {
		ssoUrl: {
			type: 'text',
			label: 'SSO URL',
			placeholder: 'https://provider.com/sso',
			help: 'The SAML SSO endpoint URL',
			required: true,
		},
		entityId: {
			type: 'text',
			label: 'Entity ID',
			placeholder: 'urn:provider:entity',
			help: 'The SAML entity ID',
			required: true,
		},
		certificate: {
			type: 'textarea',
			label: 'X.509 Certificate',
			placeholder:
				'-----BEGIN CERTIFICATE-----\n...\n-----END CERTIFICATE-----',
			help: 'The X.509 certificate for SAML verification',
			required: true,
		},
	},
	ldap: {
		server: {
			type: 'text',
			label: 'LDAP Server',
			placeholder: 'ldap://your-server.com:389',
			help: 'The LDAP server URL and port',
			required: true,
		},
		baseDn: {
			type: 'text',
			label: 'Base DN',
			placeholder: 'dc=company,dc=com',
			help: 'The base distinguished name for searches',
			required: true,
		},
		bindDn: {
			type: 'text',
			label: 'Bind DN',
			placeholder: 'cn=admin,dc=company,dc=com',
			help: 'The distinguished name for binding',
			required: true,
		},
		bindPassword: {
			type: 'password',
			label: 'Bind Password',
			help: 'The password for the bind DN',
			required: true,
		},
	},
	email: {
		passwordRequirements: {
			type: 'select',
			label: 'Password Requirements',
			help: 'Set the minimum password strength',
			defaultValue: 'medium',
			options: [
				{ label: 'Low (6+ characters)', value: 'low' },
				{ label: 'Medium (8+ chars, mixed case)', value: 'medium' },
				{ label: 'High (12+ chars, symbols)', value: 'high' },
			],
		},
		emailVerification: {
			type: 'toggle',
			label: 'Enable Email Verification',
			help: 'Require users to verify their email address',
			defaultValue: true,
		},
		passwordReset: {
			type: 'toggle',
			label: 'Allow Password Reset',
			help: 'Allow users to reset their password via email',
			defaultValue: true,
		},
	},
	apiKey: {
		apiEndpoint: {
			type: 'text',
			label: 'API Endpoint',
			placeholder: 'https://api.provider.com/auth',
			help: 'The API endpoint for authentication',
			required: true,
		},
		apiKeyHeader: {
			type: 'text',
			label: 'API Key Header',
			placeholder: 'X-API-Key',
			help: 'The header name for the API key',
			required: true,
		},
		keyFormat: {
			type: 'select',
			label: 'Key Format',
			help: 'The format of the API key',
			defaultValue: 'bearer',
			options: [
				{ label: 'Bearer Token', value: 'bearer' },
				{ label: 'API Key', value: 'api-key' },
				{ label: 'Custom', value: 'custom' },
			],
		},
	},
};
