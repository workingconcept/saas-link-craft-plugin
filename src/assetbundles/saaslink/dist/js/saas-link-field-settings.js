'use strict';

/*
 global window,
 Craft
 */

const serviceField = document.getElementById('types-workingconcept-saaslink-fields-SaasLinkField-service'),
    relationshipTypeField = document.getElementById('types-workingconcept-saaslink-fields-SaasLinkField-relationshipType');

serviceField.addEventListener('change', updateRelationshipTypeOptions);

function updateRelationshipTypeOptions(event) {
    console.log('called');
    const selectedService = serviceField.querySelector('option:checked') ? serviceField.querySelector('option:checked').value : false;
    const selectedRelationshipType = relationshipTypeField.querySelector('option:checked') ? relationshipTypeField.querySelector('option:checked').value : false;

    Craft.postActionRequest(
        window.__SAASLINK_PLUGIN.actions.fetchRelationshipTypes,
        {selectedService},
        (response, statusText, request) => {

            Array.from(relationshipTypeField.querySelectorAll('option'))
                .forEach(option => option.remove());

            const emptyOption = document.createElement('option');

            emptyOption.textContent = "Choose...";
            emptyOption.value = "";

            relationshipTypeField.appendChild(emptyOption);

            for (let i = 0; i < response.length; i++) {
                const row    = response[i];
                const option = document.createElement('option');

                option.value       = row.value;
                option.textContent = row.label;

                relationshipTypeField.appendChild(option);
            }
        }
    );
}