import React, { Component } from 'react'
import styled from 'styled-components'

const SCContentInner = styled.div`
    padding: 0px 32px;
`

class BlockContentInner extends Component {
    render() {
        const { children } = this.props

        return <SCContentInner>{children}</SCContentInner>
    }
}

export default BlockContentInner
