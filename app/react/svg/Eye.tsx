export const SVGEye = ({ style = {} }) => {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="18"
            viewBox="0 0 24 18"
            style={style}
        >
            <g
                fill="none"
                fillRule="evenodd"
                stroke="#00081A"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                transform="translate(1 1)"
            >
                <path d="M0 8s4-8 11-8 11 8 11 8-4 8-11 8S0 8 0 8z" />
                <circle cx="11" cy="8" r="3" />
            </g>
        </svg>
    );
};
