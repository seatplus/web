import AssetsController from './AssetsController'
import ContactsController from './ContactsController'
import WalletsController from './WalletsController'
import ContractsController from './ContractsController'
import CorporationHistoryController from './CorporationHistoryController'
import SkillsController from './SkillsController'
import MailsController from './MailsController'

const Character = {
    AssetsController: Object.assign(AssetsController, AssetsController),
    ContactsController: Object.assign(ContactsController, ContactsController),
    WalletsController: Object.assign(WalletsController, WalletsController),
    ContractsController: Object.assign(ContractsController, ContractsController),
    CorporationHistoryController: Object.assign(CorporationHistoryController, CorporationHistoryController),
    SkillsController: Object.assign(SkillsController, SkillsController),
    MailsController: Object.assign(MailsController, MailsController),
}

export default Character