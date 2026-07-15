/**
 * Shared mapping between the ACL form state (EsiMultiselect selections) and the backend
 * `{name, affiliated[], assigned[], permissions[]}` contract. Used by BOTH the create wizard
 * and the sectioned edit page so they never diverge.
 */

/**
 * Doomheim — EVE's graveyard corporation that no live character belongs to. Used as a sentinel:
 * affiliated as INVERSE it means "applies to everyone"; as a membership criterion it means
 * "anyone is eligible" (open to all). Mirrors `AbstractRoleService::EVERYONE_CORPORATION_ID`.
 */
export const EVERYONE_CORPORATION_ID = 1000001;

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
 * The three applies-to lists are independent: `allowed` → ALLOWED, `inverse` → INVERSE,
 * `excluded` → FORBIDDEN. Eligibility (criteria) is only sent for join methods that use it.
 * Note: `type` is NOT included here — the edit endpoints carry it in the route; the create
 * wizard adds `type: form.method` on top of this.
 */
export function buildRolePayload(data, selectedMethod) {
    const toEntities = (selections, affiliationType) => (selections ?? []).map((selection) => ({
        entity_id: selection.id,
        entity_type: selection.category,
        ...(affiliationType ? { affiliation_type: affiliationType } : {}),
    }));

    // "Applies to everything" → a single INVERSE Doomheim affiliation (everyone except a corp nobody is in).
    const affiliated = data.everything
        ? [{ entity_id: EVERYONE_CORPORATION_ID, entity_type: "corporation", affiliation_type: "inverse" }]
        : [
            ...toEntities(data.allowed, "allowed"),
            ...toEntities(data.inverse, "inverse"),
            ...toEntities(data.excluded, "forbidden"),
        ];

    // Eligibility only applies to join methods that use it. "Anyone" → the Doomheim sentinel criterion.
    let assigned = [];
    if (selectedMethod?.uses_eligibility) {
        assigned = data.anyone
            ? [{ entity_id: EVERYONE_CORPORATION_ID, entity_type: "corporation" }]
            : toEntities(data.eligibility);
    }

    return {
        name: data.name,
        affiliated,
        assigned,
        permissions: data.permissions ?? [],
    };
}
