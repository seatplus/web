import { router, usePage } from "@inertiajs/vue3";
import JoinOptInRoleController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/JoinOptInRoleController";
import ApplyToRoleController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ApplyToRoleController";
import LeaveControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/LeaveControlGroupController";

/**
 * Thin wrapper over the ACL Wayfinder actions so the discover/detail views don't hardcode URLs
 * or HTTP verbs. Each call is an Inertia visit that follows the controller's redirect and refreshes
 * the page props (so My/Available lists and membership status update in place).
 *
 * Member-management actions (approve/deny/member/moderator) are added alongside the moderator
 * surface (PR 3); this composable covers the self-service discover actions.
 */
export function useRoleActions() {
    const page = usePage();
    const options = { preserveScroll: true };

    const currentUserId = () => page.props.user.data.id;

    return {
        // opt-in: join instantly
        join: (roleId) => router.post(JoinOptInRoleController.url({ role_id: roleId }), {}, options),
        // on-request: submit an application (pending until a moderator approves)
        apply: (roleId) => router.post(ApplyToRoleController.url({ role_id: roleId }), {}, options),
        // leave / cancel a pending application for yourself
        leave: (roleId, userId = null) => router.delete(
            LeaveControlGroupController.url({ role_id: roleId, user_id: userId ?? currentUserId() }),
            options,
        ),
    };
}
