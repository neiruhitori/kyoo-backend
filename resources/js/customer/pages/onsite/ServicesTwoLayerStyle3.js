import { useState, useEffect } from 'react'
import { useNavigate, useParams, useSearchParams } from 'react-router-dom'
import { format, eachMonthOfInterval, parseISO, addDays } from 'date-fns'
import id from 'date-fns/locale/id'
import en from 'date-fns/locale/en-US'

import useBranch from '../../hooks/useBranch'
import useBranchSchedules from '../../hooks/useBranchSchedules'
import useBranchHolidays from '../../hooks/useBranchHolidays'
import useBranchServices from '../../hooks/useBranchServices'
import { fetchServiceById } from '../../api/services'

import 'react-day-picker/lib/style.css'

import { Link } from 'react-router-dom'
import DayPicker from 'react-day-picker'

import Banner from '../../components/Banner'
import Header from '../../components/Header'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatusOpen from '../../components/style1/BranchStatusOpenStyle1'
import BranchStatusClosed from '../../components/style1/BranchStatusClosedStyle1'
import SliderIndicator from '../../components/SliderIndicator'
import MainContent from '../../components/style3/MainContent'
import TextField from '../../components/style3/TextFieldStyle3'
import IconButton from '../../components/IconButton'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import CalendarWrapper from '../../components/CalendarWrapper'
import useLocalization from '../../hooks/useLocalization'
import ServiceItemSkeleton from '../../components/style3/ServiceItemSkeletonStyle3'
import ServiceItem from '../../components/style3/ServiceItemStyle3'
import ServiceCard from '../../components/style3/ServiceCardStyle3'
import Button from '../../components/style1/ButtonStyle1'
import DetailWrapper from '../../components/style3/DetailWrapper'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'
import IndustryCategoryWrapper from '../../components/style1/IndustryCategoryWrapper'
import DateSlider from '../../components/style1/DateSlider'
import SlotServiceComponent from './../../components/style1/SlotServiceComponent';
import ProfileCard from '../../components/style3/ProfileCard'
import IconWrapper from '../../components/style3/IconWrapper'

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import BoxOpenIcon from '../../icons/BoxOpenIcon'
import { fetchBranch } from '../../api/branch'
import { useQuery } from 'react-query'
import { getDayName, getFullDate } from '../../utils/date'


function ServicesTwoLayer() {
    const { branchId, serviceCategoryId } = useParams()
    const [searchParams] = useSearchParams()
    const isAllowback = searchParams.get("is_allow_back")
    const navigate = useNavigate()
    const {t, locale} = useLocalization();
    const dateLocale = locale == "id" ? id : en;

    const PAGE_TITLE = t('Appointment Queue')

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [isCalendarShow, setIsCalendarShow] = useState(false)
    const [selectedService, setSelectedService] = useState(null);
    const [selectedSlot, setSelectedSlot] = useState(null);
    const [displayStartDate, setDisplayStartDate] = useState(new Date());
    const [currentPage, setCurrentPage] = useState(1);
    const [isSlotLoading, setIsSlotLoading] = useState(false);
    const [slotList, setSlotList] = useState([]);

    const branchQuery = useQuery('branch', () => fetchBranch(branchId))
    const branchSchedulesQuery = useBranchSchedules(branchId)
    const branchHolidaysQuery = useBranchHolidays(branchId)
    const branchServicesQuery = useBranchServices(branchId, {
            queueType: 'appointment-onsite',
            date: getFullDate(selectedDate, 'en'),
            day: getDayName(selectedDate, 'en'),
        })



    const branch = branchQuery?.data
    const schedules = branchSchedulesQuery?.data
    const holidays = branchHolidaysQuery?.data
    const services = branchServicesQuery?.data

    const todaySchedule = schedules?.find(v => v.day === format(selectedDate, 'eeee').toLowerCase())
    const todayHoliday = holidays?.find(v => v.date === format(selectedDate, 'yyyy-MM-dd'))

    if(branch && branch.branch_configuration.layer === 1){
        navigate(`/customer/${branchId}/onsite/services`);
    }

    useEffect(() => {
        branchSchedulesQuery.refetch()
        branchHolidaysQuery.refetch()
        branchServicesQuery.refetch()
        setDisplayStartDate(selectedDate)
    }, [selectedDate])

    useEffect(() => {
        if (!selectedService?.id) return
        
        const loadSlots = async () => {
              setIsSlotLoading(true)
              try {
                const serviceQuery = await fetchServiceById(selectedService.id, {
                    queueType: 'appointment-onsite',
                    date: getFullDate(displayStartDate)
                })
                const slots = serviceQuery.slot.filter(v => {
                    return v.day === getDayName(displayStartDate, 'en')
                })
                setSlotList(slots || [])
              } catch (error) {
                console.error("Gagal memuat slot layanan:", error)
                setSlotList([]) 
              } finally {
                setIsSlotLoading(false)
              }
            }
            loadSlots()
          }, [selectedService, displayStartDate])
          
          useEffect(() => {
            setSelectedSlot(null);
          }, [selectedService, displayStartDate]);

    const handleSelectService = (service) => {
        if (selectedService?.id === service.id) {
            setSelectedService(null);
        } else {
            setCurrentPage(1)
            setDisplayStartDate(selectedDate);
            setSelectedService(service);
        }
    };

    const handleSelectSlot = (slot) => {
        if(slot?.max_slots === slot?.filled_slot){
            return
        }
        setSelectedSlot(slot)
    }
            
    const handleNext = () => {
        if (currentPage === 2){
            return;
        } else if(currentPage < 2) {
            setCurrentPage(2);
        }
    };
                
    const handlePrev = () => {
        if (currentPage === 1){
            return;
        } else if(currentPage > 1) {
            setCurrentPage(1);
        }
    };

    const days = Array.from({ length: 8 }, (_, i) => addDays(selectedDate, i));
    const displayedDays = currentPage === 1 ? days.slice(0, 4) : days.slice(4, 8);

    const handleSubmit = () =>{
        if(isSlotLoading || slotList?.length == 0 || !selectedSlot) return;

        const branchId = branch?.id;
        const serviceId = selectedService?.id;
        const date = format(displayStartDate, 'yyyy-MM-dd');
        const slotId = selectedSlot?.id;

        const url = `/customer/${branchId}/appointment-onsite/services/${serviceId}/visitor?date=${date}&slot=${slotId}`;
        navigate(url)
    }           

    function isBranchOpen() {
        return todaySchedule?.status === 'open' && !todayHoliday
    }

    function getClosedDayIndexes() {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

        return schedules
            .filter(v => v.status === 'closed')
            .map(v => days.indexOf(v.day))
    }

    function getMonthNames() {
        const now = new Date()

        return eachMonthOfInterval({
            start: new Date(now.getFullYear(), 0, 1),
            end: new Date(now.getFullYear(), 11, 1)
        })
            .map(v => {
                return format(v, 'MMMM', { locale: dateLocale })
            })
    }

    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) return
        setSelectedDate(day)
    }

    function handleCalendarClose() {
        setIsCalendarShow(false)
    }

    return <>
        <Banner imageUrl={branch?.photo} style={{ borderRadius:'0px' }}>
        
        <ProfileCard>
            <div style={{ display:'flex', gap:'1.2rem', marginBottom:'1rem' }}>
                <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/store.svg`}style={{ height:'4rem'}} />
              <div>
                <IndustryCategoryWrapper>
                            {branch?.industry_category.name}
                </IndustryCategoryWrapper>
                <div>
                    <BranchNameWrapper>
                        {branch?.name}
                    </BranchNameWrapper>
                </div>
              </div>
            </div>

            <hr style={{ marginBottom:'1.2rem' }}/>
                
            <div style={{ display:'flex', justifyContent: 'space-between', }}>
                    <div style={{ display:'flex', alignItems: 'center' }}>
                        <IconWrapper>
                            <ClockIcon fill='white'/>
                        </IconWrapper>
                        {isBranchOpen()
                            ? <BranchStatusOpen
                                startTime={todaySchedule?.start_time.slice(0, -3)}
                                endTime={todaySchedule?.end_time.slice(0, -3)}
                                style={{
                                    marginTop: '1rem'
                                }}
                                t={t}
                            />
                            : <BranchStatusClosed
                                style={{
                                    marginTop: '1rem'
                                }}
                                t={t}
                            />
                        }
                    </div>
                    <Link to={`/customer/${branch?.id}/appointment/detail`} style={{ alignContent:'end' }}>
                        <DetailWrapper>
                            <span style={{padding: '0 0.75rem'}}>{t('See Details')}</span>
                            <AngleRightIcon color="#33A0FF" style={{
                                display:'block',
                                margin:'auto',
                                borderRadius:'50%',
                                backgroundColor:'#fff',
                                fill:'#33A0FF',
                                padding:'3px'
                            }}/>
                        </DetailWrapper>
                    </Link>
                </div>
        </ProfileCard>
        </Banner>

        <MainContent style={{ backgroundColor: '#103c7c',}}>

            {isCalendarShow && <CalendarWrapper
                onClick={handleCalendarClose}
            >
                <DayPicker
                    onDayClick={handleDayClick}
                    months={getMonthNames()}
                    modifiers={{
                        selected: selectedDate,
                        disabled: [
                            ...holidays.map(v => parseISO(v.date)),
                            { daysOfWeek: getClosedDayIndexes() },
                            { before: new Date() }
                        ]
                    }}
                    modifiersStyles={{
                        selected: {
                            backgroundColor: '#0172CB',
                            color: '#FFFFFF'
                        }
                    }}
                />
            </CalendarWrapper>}

            <TextField
                label={t("Date")}
                style={{
                    marginBottom: '1.5rem'
                }}
                value={format(selectedDate, 'd MMMM yyyy', { locale: dateLocale })}
                readOnly
                endAdornment={
                    <IconButton style={{ background:'white' }}
                        onClick={() => setIsCalendarShow(true)}
                    >
                        <CalendarIcon height="24" width="24" />
                    </IconButton>
                }
            />

            <h4 style={{
                fontSize: '1rem',
                color: '#fff',
                marginBottom: '1.125rem'
            }}>{t('Service')}</h4>

            {branchServicesQuery.isLoading && <ServiceItemSkeleton />}

            {isBranchOpen() && services?.map(service => {
                const availableSlot = service.filledSlot < service.totalSlot
                    ? service.totalSlot - service.filledSlot
                    : 0
            
                const isSelected = selectedService?.id === service.id;

                if (!service.is_show) return

                return <div key={service.id} style={{
                    marginBottom: '1.125rem'
                }} >
                    <ServiceItem
                        onClick={() => handleSelectService(service)}
                        title={service.name}
                        isSelected={isSelected}
                        action={{
                            label: t("Total Available Slots"),
                            value: availableSlot,
                            total: service.totalSlot
                        }}
                        subtitle={<div style={{
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <span>
                                {
                                    service.slots.length
                                        ? service.slots.length + ` ${t('Time Sessions')}`
                                        : t('No Time Sessions')
                                }
                            </span>
                        </div>}
                        timeSlot={<div style={{
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <span>
                            {
                                service.slots.length
                                ? (() => {
                                    const times = service.slots.map(slot => ({
                                        start: slot.start_time,
                                        end: slot.end_time
                                    }));

                                    const minStart = times.reduce((min, t) => t.start < min ? t.start : min, times[0].start);
                                    const maxEnd = times.reduce((max, t) => t.end > max ? t.end : max, times[0].end);

                                    return `${minStart} - ${maxEnd}`;
                                    })()
                                : ''
                            }
                            </span>
                        </div>}
                    />
                {isSelected && (
                <ServiceCard>
                <div style={{ flex:'1' }}>
                <p style={{marginBottom: '1rem'}}>
                    {t('Date')}
                </p>

                <div style={{ display:'flex', justifyContent:'space-between', marginBottom:'1rem' }}>
                    <h4 style={{ marginBottom: '1rem'}}>
                    { format(displayStartDate, 'd MMMM yyyy', { locale: dateLocale })}
                    </h4>
                    <div style={{ display:'flex' }}>
                            <AngleRightIcon 
                            onClick={handlePrev}
                            style={{ transform: 'scaleX(-1)', marginRight:'0.5rem', cursor:'pointer',
                                    fill: currentPage == 1 ? '#8e8e8e':'#000',
                             }} />
                       
                            <AngleRightIcon
                             onClick={handleNext}
                             style={{ cursor:'pointer',  fill: currentPage == 2 ? '#8e8e8e':'#000',}}
                            />
                    </div>
                </div>
                
        <div style={{ display: 'flex', overflowX: 'auto', marginBottom:'2rem' }}>
            {displayedDays.map((day, index) => (
                <DateSlider
                    key={index}
                    onClick={() => setDisplayStartDate(day)}
                    displayStartDate={displayStartDate}
                    day={day}
                >
                    <div>{format(day, 'EEE', { locale: dateLocale })}</div>
                    <div>{format(day, 'd', { locale: dateLocale })}</div>
                </DateSlider>
            ))}
        </div>
                <p style={{marginBottom: '1rem'}}>
                    {t('Service')}
                </p>
                <div style={{ display: 'flex', overflowX: 'auto'}}>
                        { isSlotLoading && <p>Loading..</p>}
                        { !isSlotLoading && slotList?.length > 0 && (slotList.map(slot => (
                                            
                                        <SlotServiceComponent 
                                            onClick={() => handleSelectSlot(slot)} 
                                            key={slot.id}
                                            selectedSlotId={selectedSlot?.id}
                                            slotId={slot?.id}
                                            startTime={slot?.start_time}
                                            endTime={slot?.end_time}
                                            usedSlot={slot?.max_slots - slot?.filled_slot}
                                            maxSlots={slot?.max_slots}
                                            >
                                         </SlotServiceComponent>
                                        ))
                                )}
                                {!isSlotLoading && slotList?.length === 0 && (
                                    <p>Tidak ada layanan</p>
                                  )}
                </div>
            </div> {/*main wrapper*/}
                <Button 
                color= { isSlotLoading ||
                         slotList?.length === 0 ||
                         !selectedSlot ? 'secondary' : 'primary'}
                type="submit" 
                style={{width: '100%',
                        fontSize: '1rem',
                        marginBottom: '.5rem'
                }} 
                disabled={isSlotLoading || 
                          slotList?.length === 0 ||
                          !selectedSlot}
                onClick={handleSubmit}
                >{t('Next')}</Button>
                
                </ServiceCard>
                )}
            </div>

            })}


            {!isBranchOpen() && <div style={{
                flex: '1 1 0%',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center'
            }}>
                <div style={{
                    display: 'inline-flex',
                    borderRadius: '99999999px',
                    backgroundColor: '#F5F5F5',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '7.5rem',
                    width: '7.5rem',
                    marginBottom: '1.5rem'
                }}>
                    <BoxOpenIcon width="5rem" height="5rem" color="#A5A5A5" />
                </div>
                <h4>{t('No Services')}</h4>
                <p style={{
                    textAlign: 'center',
                    width: '280px',
                    marginTop: '0.5rem',
                    color: '#A5A5A5',
                    fontSize: '.875rem'
                }}>
                    {t('Select another date to find available services')}
                </p>
            </div>}

            {/* {services && services.length == 0 && isBranchOpen() && <div style={{
                    flex: '1 1 0%',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center'
                }}>
                    <div style={{
                        display: 'inline-flex',
                        borderRadius: '99999999px',
                        backgroundColor: '#F5F5F5',
                        justifyContent: 'center',
                        alignItems: 'center',
                        height: '7.5rem',
                        width: '7.5rem',
                        marginBottom: '1.5rem'
                    }}>
                        <BoxOpenIcon width="5rem" height="5rem" color="#A5A5A5" />
                    </div>
                    <h4>Tidak Ada Layanan</h4>
                    <p style={{
                        textAlign: 'center',
                        width: '280px',
                        marginTop: '0.5rem',
                        color: '#A5A5A5',
                        fontSize: '.875rem'
                    }}>
                        Pilih kategori lain untuk menemukan layanan yang tersedia
                    </p>
                </div>} */}
        </MainContent>
    </>
}

export default ServicesTwoLayer

