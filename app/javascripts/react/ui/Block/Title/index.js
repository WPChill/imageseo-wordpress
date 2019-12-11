import React, { Component } from 'react'
import styled from 'styled-components'

const SCH2 = styled.h2`
    font-size: 18px;
    font-weight: bold;
    position: relative;
    color: #212529;
    margin: 32px;
    margin-bottom: 24px;
`

class BlockTitle extends Component {
    render() {
        const { children } = this.props

        return <SCH2>{children}</SCH2>
    }
}

export default BlockTitle
