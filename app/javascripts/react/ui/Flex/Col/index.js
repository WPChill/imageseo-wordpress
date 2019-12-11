import * as React from 'react'
import styled from 'styled-components'
import { isNil } from 'lodash'

import SCResponsive from '../Responsive'

const SCCol = styled(SCResponsive)`
    position: relative;
    ${props =>
        !isNil(props.offset) &&
        props.offset > 0 &&
        `margin-left:${(100 / props.theme.col) * props.offset}%;`}
        
    ${props =>
        !isNil(props.gutter) &&
        props.gutter > 0 ?
        `margin:${props.gutter}px;` : `margin:${props.theme.gutter}px;`}

    ${props =>
        !isNil(props.span) && props.span > 0
            ? `width:${(100 / props.theme.col) * props.span}%;`
            : `width:100%;`}

    @media (min-width: 576px) {
        ${props =>
            !isNil(props.xs) &&
            props.xs > 0 &&
            `width:${(100 / props.theme.col) * props.xs}%;`}
    }
    @media (min-width: 768px) {
        ${props =>
            !isNil(props.sm) &&
            props.sm > 0 &&
            `width:${(100 / props.theme.col) * props.sm}%;`}
    }
    @media (min-width: 960px) {
        ${props =>
            !isNil(props.md) &&
            props.md > 0 &&
            `width:${(100 / props.theme.col) * props.md}%;`}
    }
    @media (min-width: 1140px) {
        ${props =>
            !isNil(props.lg) &&
            props.lg > 0 &&
            `width:${(100 / props.theme.col) * props.lg}%;`}
    }
`

function Col({ style = {}, children, ...rest }) {
    return (
        <SCCol style={style} {...rest}>
            {children}
        </SCCol>
    )
}

export default Col
