function expirationDate(time) {
    return new Date(new Date().getTime() + milliseconds(time.hours, time.minutes, time.seconds))
}

function milliseconds(hours, minutes, seconds) {
    const h = hours * 60 * 60
    const m = minutes * 60
    const s = seconds
    return (h + m + s) * 1000
}

module.exports = expirationDate