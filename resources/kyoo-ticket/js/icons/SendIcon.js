import SvgIcon from '../components/SvgIcon'

export default function PhoneIcon(props) {
    return <SvgIcon {...props} viewBox="0 0 20 20" fill="none">
        <path d="M6.16665 5.26655L13.2417 2.90822C16.4167 1.84988 18.1417 3.58322 17.0917 6.75822L14.7333 13.8332C13.15 18.5916 10.55 18.5916 8.96665 13.8332L8.26665 11.7332L6.16665 11.0332C1.40832 9.44989 1.40832 6.85822 6.16665 5.26655Z" stroke={props.color || '#000000'} strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M8.42505 11.375L11.4084 8.3833" stroke={props.color || '#000000'} strokeLinecap="round" strokeLinejoin="round"/>
    </SvgIcon>
}