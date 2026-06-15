import SchedulesIndex from './SchedulesIndex'
import SchedulesPost from './SchedulesPost'
import SchedulesCreate from './SchedulesCreate'
import ScheduleDetail from './ScheduleDetail'
import SchedulesDelete from './SchedulesDelete'

const Schedules = {
    SchedulesIndex: Object.assign(SchedulesIndex, SchedulesIndex),
    SchedulesPost: Object.assign(SchedulesPost, SchedulesPost),
    SchedulesCreate: Object.assign(SchedulesCreate, SchedulesCreate),
    ScheduleDetail: Object.assign(ScheduleDetail, ScheduleDetail),
    SchedulesDelete: Object.assign(SchedulesDelete, SchedulesDelete),
}

export default Schedules