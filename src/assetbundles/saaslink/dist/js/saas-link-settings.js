'use strict';

/*
 global window,
 Craft
 */

const trelloApiKeyField = document.getElementById('settings-trelloApiKey'),
    trelloApiTokenField = document.getElementById('settings-trelloApiToken'),
    trelloOrganizationIdField = document.getElementById('settings-trelloOrganizationId');

trelloApiKeyField.addEventListener('change', updateTrelloOrganizationOptions);
trelloApiTokenField.addEventListener('change', updateTrelloOrganizationOptions);

function updateTrelloOrganizationOptions(event) {
    const selectedOrganizationId = trelloOrganizationIdField.value;
    const trelloKey = trelloApiKeyField.value;
    const trelloToken = trelloApiTokenField.value;

    if (trelloApiKeyField.value && trelloApiTokenField.value) {
        Craft.postActionRequest(
            window.__SAASLINK_PLUGIN.actions.fetchOrganizationOptions,
            {trelloKey, trelloToken},
            (response, statusText, request) => {
                console.log(response.length);
                if (response.length === 0) {
                    trelloOrganizationIdField.disabled = true;
                    return;
                }

                Array.from(trelloOrganizationIdField.querySelectorAll('option'))
                    .forEach(option => option.remove());

                const emptyOption = document.createElement('option');

                emptyOption.textContent = "Choose...";
                emptyOption.value = "";

                trelloOrganizationIdField.appendChild(emptyOption);

                for (let i = 0; i < response.length; i++) {
                    const row    = response[i];
                    const option = document.createElement('option');

                    option.value       = row.value;
                    option.textContent = row.label;

                    trelloOrganizationIdField.appendChild(option);
                }

                if (selectedOrganizationId) {
                    trelloOrganizationIdField.value = selectedOrganizationId;
                }

                trelloOrganizationIdField.disabled = false;
            }
        );
    }
}