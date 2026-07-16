import { router, usePage } from "@inertiajs/vue3";
import JoinOptInRoleController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/JoinOptInRoleController";
import ApplyToRoleController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ApplyToRoleController";
import LeaveControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/LeaveControlGroupController";
import ApproveApplicationController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ApproveApplicationController";
import DenyApplicationController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/DenyApplicationController";
import AddManualMemberController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/AddManualMemberController";
import RemoveMemberController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RemoveMemberController";
import AddModeratorController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/AddModeratorController";
import RemoveModeratorController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RemoveModeratorController";

/**
 * Thin wrapper over the ACL Wayfinder actions so the views don't hardcode URLs or HTTP verbs.
 * Each call is an Inertia visit that follows the controller's redirect and refreshes the page
 * props in place. Covers the self-service discover actions (join/apply/leave) and the moderator /
 * member-management actions (approve/deny, member add/kick, moderator add/remove).
 */
export function useRoleActions() {
    const page = usePage();
    const options = { preserveScroll: true };

    const currentUserId = () => page.props.user.data.id;

    return {
        // --- discover (self-service) ---
        join: (roleId) => router.post(JoinOptInRoleController.url({ role_id: roleId }), {}, options),
        apply: (roleId) => router.post(ApplyToRoleController.url({ role_id: roleId }), {}, options),
        leave: (roleId, userId = null) => router.delete(
            LeaveControlGroupController.url({ role_id: roleId, user_id: userId ?? currentUserId() }),
            options,
        ),

        // --- moderator: applications ---
        approve: (roleId, userId) => router.post(ApproveApplicationController.url({ role_id: roleId, user_id: userId }), {}, options),
        deny: (roleId, userId) => router.delete(DenyApplicationController.url({ role_id: roleId, user_id: userId }), options),

        // --- members (moderator / admin) ---
        addMember: (roleId, userId) => router.post(AddManualMemberController.url({ role_id: roleId, user_id: userId }), {}, options),
        removeMember: (roleId, userId) => router.delete(RemoveMemberController.url({ role_id: roleId, user_id: userId }), options),

        // --- moderators (admin) ---
        addModerator: (roleId, userId) => router.post(AddModeratorController.url({ role_id: roleId, user_id: userId }), {}, options),
        removeModerator: (roleId, userId) => router.delete(RemoveModeratorController.url({ role_id: roleId, user_id: userId }), options),
    };
}
