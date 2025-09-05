import { Calendar } from '@fullcalendar/core'
import interactionPlugin from '@fullcalendar/interaction'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import multiMonthPlugin from '@fullcalendar/multimonth'
import scrollGridPlugin from '@fullcalendar/scrollgrid'
import timelinePlugin from '@fullcalendar/timeline'
import adaptivePlugin from '@fullcalendar/adaptive'
import resourcePlugin from '@fullcalendar/resource'
import resourceDayGridPlugin from '@fullcalendar/resource-daygrid'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid'
import rrulePlugin from '@fullcalendar/rrule'
import momentPlugin from '@fullcalendar/moment'
import momentTimezonePlugin from '@fullcalendar/moment-timezone'
import locales from '@fullcalendar/core/locales-all'

export default function fullcalendar({
    locale,
    plugins,
    schedulerLicenseKey,
    timeZone,
    config,
    editable,
    selectable,
    eventClassNames,
    eventContent,
    eventDidMount,
    eventWillUnmount,
}) {
    return {
        init() {
            /** @type Calendar */
            const calendar = new Calendar(this.$el, {
                headerToolbar: {
                    'left': 'prev,next today',
                    'center': 'title',
                    'right': 'dayGridMonth,dayGridWeek,dayGridDay',
                },
                plugins: plugins.map(plugin => availablePlugins[plugin]),
                locale,
                schedulerLicenseKey,
                timeZone,
                editable,
                selectable,
                ...config,
                locales,
                eventClassNames,
                eventContent,
                eventDidMount,
                eventWillUnmount,
                events: (info, successCallback, failureCallback) => {
                    this.$wire.fetchEvents({ start: info.startStr, end: info.endStr, timezone: info.timeZone })
                        .then(successCallback)
                        .catch(failureCallback)
                },
                eventClick: ({ event, jsEvent }) => {
                    jsEvent.preventDefault()

                    if (event.url) {
                        const isNotPlainLeftClick = e => (e.which > 1) || (e.altKey) || (e.ctrlKey) || (e.metaKey) || (e.shiftKey)
                        return window.open(event.url, (event.extendedProps.shouldOpenUrlInNewTab || isNotPlainLeftClick(jsEvent)) ? '_blank' : '_self')
                    }

                    this.$wire.onEventClick(event)
                },
                eventDrop: async ({ event, oldEvent, relatedEvents, delta, oldResource, newResource, revert }) => {
                    const shouldRevert = await this.$wire.onEventDrop(event, oldEvent, relatedEvents, delta, oldResource, newResource)

                    if (typeof shouldRevert === 'boolean' && shouldRevert) {
                        revert()
                    }
                },
                eventResize: async ({ event, oldEvent, relatedEvents, startDelta, endDelta, revert }) => {
                    const shouldRevert = await this.$wire.onEventResize(event, oldEvent, relatedEvents, startDelta, endDelta)

                    if (typeof shouldRevert === 'boolean' && shouldRevert) {
                        revert()
                    }
                },
                dateClick: ({ dateStr, allDay, view, resource }) => {
                    if (!selectable) return;
                    this.$wire.onDateSelect(dateStr, null, allDay, view, resource)
                },
                select: ({ startStr, endStr, allDay, view, resource }) => {
                    if (!selectable) return;
                    this.$wire.onDateSelect(startStr, endStr, allDay, view, resource)
                },
            })

            calendar.render()

            window.addEventListener('filament-fullcalendar--refresh', () => calendar.refetchEvents())
            window.addEventListener('filament-fullcalendar--prev', () => calendar.prev())
            window.addEventListener('filament-fullcalendar--next', () => calendar.next())
            window.addEventListener('filament-fullcalendar--today', () => calendar.today())
            window.addEventListener('filament-fullcalendar--goto', (event) => calendar.gotoDate(event.detail.date))
        },
    }
}

const availablePlugins = {
    'interaction': interactionPlugin,
    'dayGrid': dayGridPlugin,
    'timeGrid': timeGridPlugin,
    'list': listPlugin,
    'multiMonth': multiMonthPlugin,
    'scrollGrid': scrollGridPlugin,
    'timeline': timelinePlugin,
    'adaptive': adaptivePlugin,
    'resource': resourcePlugin,
    'resourceDayGrid': resourceDayGridPlugin,
    'resourceTimeline': resourceTimelinePlugin,
    'resourceTimeGrid': resourceTimeGridPlugin,
    'rrule': rrulePlugin,
    'moment': momentPlugin,
    'momentTimezone': momentTimezonePlugin,
}
