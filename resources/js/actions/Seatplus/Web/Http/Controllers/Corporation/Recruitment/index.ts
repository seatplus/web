import ApplicationsController from './ApplicationsController'
import EnlistmentsController from './EnlistmentsController'
import GetRecruitmentIndexController from './GetRecruitmentIndexController'
import ImpersonateRecruit from './ImpersonateRecruit'

const Recruitment = {
    ApplicationsController: Object.assign(ApplicationsController, ApplicationsController),
    EnlistmentsController: Object.assign(EnlistmentsController, EnlistmentsController),
    GetRecruitmentIndexController: Object.assign(GetRecruitmentIndexController, GetRecruitmentIndexController),
    ImpersonateRecruit: Object.assign(ImpersonateRecruit, ImpersonateRecruit),
}

export default Recruitment