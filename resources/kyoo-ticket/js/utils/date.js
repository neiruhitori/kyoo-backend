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
    return date.toISOString().slice(0, 10)
}

export function getAbrvDate(date) {
    return `${date.getDate()} ${MONTHS_ABBR[defaultLocale][date.getMonth()]} ${date.getFullYear()}`
}