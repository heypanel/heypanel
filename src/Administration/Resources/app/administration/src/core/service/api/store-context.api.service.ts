/**
 * @sw-package discovery
 */

import type { AxiosInstance } from 'axios';
import ApiService from '../api.service';
import type { LoginService } from '../login.service';

/**
 * Gateway for the API end point "channel-context"
 * Uses the _proxy endpoint of the admin api to connect to the client-api endpoint cart
 * @class
 * @extends ApiService
 */
class StoreContextService extends ApiService {
    constructor(httpClient: AxiosInstance, loginService: LoginService, apiEndpoint = 'channel-context') {
        super(httpClient, loginService, apiEndpoint, 'application/json');

        this.name = 'contextStoreService';
    }

    updateCustomerContext(
        memberId: string,
        channelId: string,
        contextToken: string,
        additionalParams = {},
        additionalHeaders = {},
        permissions = ['allowProductPriceOverwrites'],
    ) {
        const route = '_proxy/switch-member';
        const headers = this.getBasicHeaders({
            ...additionalHeaders,
            'sw-context-token': contextToken,
        });

        return this.httpClient.patch(
            route,
            {
                memberId: memberId,
                channelId: channelId,
                permissions: permissions,
            },
            { ...additionalParams, headers },
        );
    }

    getChannelContext(
        channelId: string,
        contextToken: string | null,
        additionalParams = {},
        additionalHeaders = {},
    ) {
        const route = `_proxy/client-api/${channelId}/context`;
        const headers = this.getBasicHeaders({
            ...additionalHeaders,
            'sw-context-token': contextToken,
        });

        return this.httpClient.get(route, { ...additionalParams, headers });
    }

    generateImitateCustomerToken(memberId: string, channelId: string, additionalParams = {}, additionalHeaders = {}) {
        const route = '_proxy/generate-imitate-member-token';
        const headers = this.getBasicHeaders(additionalHeaders);

        return this.httpClient.post(
            route,
            {
                memberId,
                channelId,
            },
            { ...additionalParams, headers },
        );
    }

    redirectToChannelUrl(channelDomainUrl: string, token: string, memberId: string, userId: string) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${channelDomainUrl}/account/login/imitate-member`;
        form.target = '_blank';
        document.body.appendChild(form);

        this.#createHiddenInput(form, 'token', token);
        this.#createHiddenInput(form, 'memberId', memberId);
        this.#createHiddenInput(form, 'userId', userId);

        form.submit();
        form.remove();
    }

    #createHiddenInput(form: HTMLFormElement, name: string, value: string) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default StoreContextService;
