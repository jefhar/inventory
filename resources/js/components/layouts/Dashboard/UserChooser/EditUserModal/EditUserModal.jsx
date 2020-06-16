import React from 'react'
import PropTypes from 'prop-types'
import { Button, Input, Label, Modal, ModalBody, ModalHeader } from 'reactstrap'
import EditUserIcon from '../../../../Icons/EditUserIcon/EditUserIcon'

const propTypes = {
  onClick: PropTypes.func,
  show: PropTypes.bool,
  toggle: PropTypes.func,
  users: PropTypes.array,
}

const EditUserModal = (props) => {
  if (!props.show) {
    return null
  }
  const users = props.users.map((user) => (
    <option key={user.email}>{user.name}</option>
  ))

  return (
    <Modal color="primary" isOpen={props.show} toggle={props.toggle}>
      <ModalHeader toggle={props.toggle}>
        <EditUserIcon /> Choose Existing User
      </ModalHeader>
      <ModalBody>
        <Label for="userList">
          <EditUserIcon /> Choose an Existing User:
        </Label>
        <Input id="usersList" name="select" size="8" type="select">
          {users}
        </Input>
      </ModalBody>
      <ModalHeader>
        <Button
          className="mt-3"
          color="primary"
          outline={true}
          onClick={props.onClick}
        >
          <>
            <EditUserIcon /> Choose User
          </>
        </Button>
      </ModalHeader>
    </Modal>
  )
}

EditUserModal.propTypes = propTypes

export default EditUserModal
