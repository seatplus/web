import ShowControlGroupsController from './ShowControlGroupsController'
import ListControlGroupsController from './ListControlGroupsController'
import ManageMembersController from './ManageMembersController'
import ListMembersController from './ListMembersController'
import ApplyToRoleController from './ApplyToRoleController'
import JoinOptInRoleController from './JoinOptInRoleController'
import ApproveApplicationController from './ApproveApplicationController'
import DenyApplicationController from './DenyApplicationController'
import LeaveControlGroupController from './LeaveControlGroupController'
import CreateControlGroupController from './CreateControlGroupController'
import DeleteControlGroupController from './DeleteControlGroupController'
import SearchAffiliatableController from './SearchAffiliatableController'
import ManageControlGroupMembersController from './ManageControlGroupMembersController'
import ListUserController from './ListUserController'
import ShowControlGroupController from './ShowControlGroupController'
import ManageRoleController from './ManageRoleController'
import AddModeratorController from './AddModeratorController'
import RemoveModeratorController from './RemoveModeratorController'
import AddManualMemberController from './AddManualMemberController'
import RemoveMemberController from './RemoveMemberController'

const AccessControl = {
    ShowControlGroupsController: Object.assign(ShowControlGroupsController, ShowControlGroupsController),
    ListControlGroupsController: Object.assign(ListControlGroupsController, ListControlGroupsController),
    ManageMembersController: Object.assign(ManageMembersController, ManageMembersController),
    ListMembersController: Object.assign(ListMembersController, ListMembersController),
    ApplyToRoleController: Object.assign(ApplyToRoleController, ApplyToRoleController),
    JoinOptInRoleController: Object.assign(JoinOptInRoleController, JoinOptInRoleController),
    ApproveApplicationController: Object.assign(ApproveApplicationController, ApproveApplicationController),
    DenyApplicationController: Object.assign(DenyApplicationController, DenyApplicationController),
    LeaveControlGroupController: Object.assign(LeaveControlGroupController, LeaveControlGroupController),
    CreateControlGroupController: Object.assign(CreateControlGroupController, CreateControlGroupController),
    DeleteControlGroupController: Object.assign(DeleteControlGroupController, DeleteControlGroupController),
    SearchAffiliatableController: Object.assign(SearchAffiliatableController, SearchAffiliatableController),
    ManageControlGroupMembersController: Object.assign(ManageControlGroupMembersController, ManageControlGroupMembersController),
    ListUserController: Object.assign(ListUserController, ListUserController),
    ShowControlGroupController: Object.assign(ShowControlGroupController, ShowControlGroupController),
    ManageRoleController: Object.assign(ManageRoleController, ManageRoleController),
    AddModeratorController: Object.assign(AddModeratorController, AddModeratorController),
    RemoveModeratorController: Object.assign(RemoveModeratorController, RemoveModeratorController),
    AddManualMemberController: Object.assign(AddManualMemberController, AddManualMemberController),
    RemoveMemberController: Object.assign(RemoveMemberController, RemoveMemberController),
}

export default AccessControl