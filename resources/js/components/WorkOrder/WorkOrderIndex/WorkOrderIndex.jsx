//import React, { Component } from "react";
import * as React from 'react'
import ReactDOM from 'react-dom'
import { Container } from 'reactstrap'

class WorkOrderIndex extends React.Component {
  render() {
    return <Container>WorkOrderIndex in JSX. Bitchin!</Container>
  }
}

export default WorkOrderIndex

if (document.getElementById('workorders_index')) {
  console.log('got workorders_index')
  ReactDOM.render(
    <WorkOrderIndex />,
    document.getElementById('workorders_index')
  )
}
