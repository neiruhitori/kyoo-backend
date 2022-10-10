export function getFirstVisibleDay(date) {
    return moment(date).startOf('month').startOf('week').toDate()
}

export function getEndVisibleDay(date) {
    return moment(date).endOf('month').endOf('week').toDate()
}

export function getVisibleDays(date) {
    const days = []

    let currentDate = getFirstVisibleDay(date)
    const endDate = getEndVisibleDay(date)

    while (currentDate < endDate) {
        days.push(currentDate)

        currentDate = moment(currentDate).add(1, 'd').toDate()
    }
    
    return days
}