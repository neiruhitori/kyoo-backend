import { useQuery } from 'react-query'

import { fetchSlotsByServiceId } from '../api/timeslots'

export default function useServiceSlot(serviceId, date) {
  return useQuery(
    ['service.slots', serviceId,date],
    () => fetchSlotsByServiceId(serviceId,date)
  )
}