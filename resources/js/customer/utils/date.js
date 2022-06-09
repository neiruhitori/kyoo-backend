const defaultLocale = 'id'
const DAYS = {
    'en': [
        'sunday', 'monday', 'tuesday',
        'wednesday', 'thursday', 'friday',
        'saturday'
    ],
    'id': [
        'minggu', 'senin', 'selasa',
        'rabu', 'kamis', 'jumat',
        'sabtu'
    ]
}
const DAYS_ABBR = {
    'en': [
        'sun', 'mon', 'tue',
        'wed', 'thu', 'fri',
        'sat'
    ],
    'id': [
        'min', 'sen', 'sel',
        'rab', 'kam', 'jum',
        'sab'
    ]
}
const MONTHS = {
    'id': [
        'Januari', 'Februari', 'Maret',
        'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September',
        'Oktober', 'November', 'Desember'
    ],
    'en': [
        'January', 'February', 'March',
        'April', 'May', 'June',
        'July', 'August', 'September',
        'October', 'November', 'December'
    ]
}
const MONTHS_ABBR = {
    'id': [
        'Jan', 'Feb', 'Mar',
        'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep',
        'Okt', 'Nov', 'Des'
    ],
    'en': [
        'Jan', 'Feb', 'Mar',
        'Apr', 'Mey', 'Jun',
        'Jul', 'Aug', 'Sep',
        'Oct', 'Nov', 'Dec'
    ]
}

export function format(date, locale = defaultLocale) {
    return `${date.getDate()} ${MONTHS[locale][date.getMonth()]} ${date.getFullYear()}`
}

export function formatDatetime(date, locale = defaultLocale) {
    return `${date.getDate()} ${MONTHS[locale][date.getMonth()]} ${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
}

function pad(number) {
    return number < 10 ? `0${number}` : number
}

export function getDayName(date, locale = defaultLocale) {
    return DAYS[locale][date.getDay()]
}

export function getMonthNames(locale = defaultLocale) {
    return MONTHS[locale]
}

export function getDaysName(locale = defaultLocale) {
    return DAYS[locale]
}

export function getMonthAbrvName(date, locale = defaultLocale) {
    return MONTHS_ABBR[locale][date.getMonth()]
}

export function getDayIndex(dayName) {
    const days = {
        'sunday': 0,
        'monday': 1,
        'tuesday': 2,
        'wednesday': 3,
        'thursday': 4,
        'friday': 5,
        'saturday': 6
    }

    return days[dayName]
}

export function getFullDate(date) {
    const day = date.getDate() < 10 ? `0${date.getDate()}` : date.getDate()
    const month = date.getMonth() + 1 < 10 ? `0${date.getMonth() + 1}` : date.getMonth() + 1

    return date.getFullYear() + '-' + month + '-' + day
}

export function getAbrvDate(date) {
    return `${date.getDate()} ${MONTHS_ABBR[defaultLocale][date.getMonth()]} ${date.getFullYear()}`
}

export function formatBrowser(date) {
    if (!date) return null

    const dateArr = date.split('-'),
        years = parseInt(dateArr[0]),
        months = parseInt(dateArr[1]) - 1,
        days = parseInt(dateArr[2].slice(0, 2))
    
    const times = dateArr[2].split(':')
    let hours = 0,
        minutes = 0,
        seconds = 0
    if (times.length) {
        hours = parseInt(times[0].slice(-2))
        minutes = parseInt(times[1])
        seconds = parseInt(times[2])
    }

    return new Date(years, months, days, hours, minutes, seconds)
}