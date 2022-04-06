import SvgIcon from '../components/SvgIcon'

export default function ArrowDownIcon(props) {
    return <SvgIcon {...props} viewBox="0 0 20 20" fill="none">
        <path d="M3.4001 7.42505L8.83343 12.8584C9.4751 13.5 10.5251 13.5 11.1668 12.8584L16.6001 7.42505" stroke={props.color || '#000000'} strokeWidth="1.5" strokeMiterlimit="10" strokeLinecap="round" strokeLinejoin="round"/>
    </SvgIcon>
}