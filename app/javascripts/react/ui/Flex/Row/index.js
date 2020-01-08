import * as React from 'react'
import styled from 'styled-components'
import { isNil } from 'lodash'

const SCRow = styled.div`
    display: flex;
    ${props => !isNil(props.align) && `align-items:${props.align}`}
    ${props => !isNil(props.justify) && `justify-content:${props.justify}`}
`

function Row({ style = {}, children, ...rest }) {
    return (
        <SCRow style={style} {...rest}>
            {children}
        </SCRow>
    )
}

export default Row
