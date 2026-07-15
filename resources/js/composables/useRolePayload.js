/**
 * Shared mapping between the ACL form state (EsiMultiselect selections) and the backend
 * `{name, affiliated[], assigned[], permissions[]}` contract. Used by BOTH the create wizard
 * and the sectioned edit page so they never diverge.
 */

/** Resource entities `{id, entity_type, name}` → EsiMultiselect selections `{id, name, category}`. */
export function entitiesToSelections(entities) {
    return (entities ?? []).map((entity) => ({
        id: entity.id,
        name: entity.name,
        category: entity.entity_type,
    }));
}

/**
 * Build the common role payload from form data + the selected join-method metadata.
 * `mode === 'everyone_except'` → INVERSE affiliations; the excluded list → FORBIDDEN.
 * Eligibility (criteria) is only sent for join methods that use it.
 * Note: `type` is NOT included here — the edit endpoints carry it in the route; the create
 * wizard adds `type: form.method` on top of this.
 */
export function buildRolePayload(data, selectedMethod) {
    const toEntities = (selections, affiliationType) => (selections ?? []).map((selection) => ({
        entity_id: selection.id,
        entity_type: selection.category,
        ...(affiliationType ? { affiliation_type: affiliationType } : {}),
    }));

    return {
        name: data.name,
        affiliated: [
            ...toEntities(data.included, data.mode === "everyone_except" ? "inverse" : "allowed"),
            ...toEntities(data.excluded, "forbidden"),
        ],
        assigned: selectedMethod?.uses_eligibility ? toEntities(data.eligibility) : [],
        permissions: data.permissions ?? [],
    };
}
