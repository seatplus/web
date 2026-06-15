import Wallet from './Wallet'
import MemberTracking from './MemberTracking'
import Recruitment from './Recruitment'
import MemberCompliance from './MemberCompliance'

const Corporation = {
    Wallet: Object.assign(Wallet, Wallet),
    MemberTracking: Object.assign(MemberTracking, MemberTracking),
    Recruitment: Object.assign(Recruitment, Recruitment),
    MemberCompliance: Object.assign(MemberCompliance, MemberCompliance),
}

export default Corporation