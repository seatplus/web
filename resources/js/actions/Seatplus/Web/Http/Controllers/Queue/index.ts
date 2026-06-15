import QueueController from './QueueController'
import DispatchJobController from './DispatchJobController'

const Queue = {
    QueueController: Object.assign(QueueController, QueueController),
    DispatchJobController: Object.assign(DispatchJobController, DispatchJobController),
}

export default Queue