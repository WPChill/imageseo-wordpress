import React from 'react'
import styled from 'styled-components'

const SCTextarea = styled.textarea`
    border-radius: 4px;
    border: solid 1px var(--grey-dark);
    background-color: var(--grey-light);
    padding: 5px;
    width: 100%;
    min-width: 100%;
    min-height: 120px;
`

function Textarea({ children, ...rest }) {
    return <SCTextarea {...rest}>{children}</SCTextarea>
}

export default Textarea
