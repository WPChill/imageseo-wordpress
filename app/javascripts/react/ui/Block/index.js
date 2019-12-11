import React, { Component } from 'react'
import styled from 'styled-components'
import BlockTitle from './Title'
import BlockContentInner from './ContentInner'

const SCBlock = styled.div`
    background-color: #ffffff;
    border: 1px solid #c8d0dd;
    height: 100%;
    padding-bottom: 32px;
    border-radius: 3px;
    box-shadow: 0 3px 6px 0 rgba(33, 43, 61, 0.1),
        0 0 6px 0 rgba(33, 43, 61, 0.05);
    margin-bottom: 20px;
`

class Block extends Component {
    static Title = BlockTitle
    static ContentInner = BlockContentInner
    render() {
        const { children, style = {} } = this.props

        return <SCBlock style={style}>{children}</SCBlock>
    }
}

export default Block
