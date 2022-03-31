import SvgIcon from '../components/SvgIcon'

function LocationIcon(props) {
    const color = props.color || 'black'

    return <SvgIcon {...props} viewBox="0 0 21 21" fill="none">
        <path d="M10.4999 11.9417C11.9358 11.9417 13.0999 10.7776 13.0999 9.3417C13.0999 7.90576 11.9358 6.7417 10.4999 6.7417C9.06396 6.7417 7.8999 7.90576 7.8999 9.3417C7.8999 10.7776 9.06396 11.9417 10.4999 11.9417Z" stroke={color}/>
        <path d="M3.51675 7.82484C5.15842 0.608173 15.8501 0.616506 17.4834 7.83317C18.4417 12.0665 15.8084 15.6498 13.5001 17.8665C11.8251 19.4832 9.17508 19.4832 7.49175 17.8665C5.19175 15.6498 2.55842 12.0582 3.51675 7.82484Z" stroke={color}/>
    </SvgIcon>
}

export default LocationIcon