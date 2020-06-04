import * as React from 'react'
import PropTypes from 'prop-types'
import ReactDOM from 'react-dom'
import { Button } from 'reactstrap'

const propTypes = {
  data: PropTypes.object,
}
const defaultProps = {
  data: {
    cartId: null,
    disabled: false,
    productId: null,
    type: 'cart',
    text: '',
  },
}

class DropButton extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
    this.handleClick = this.handleClick.bind(this)
  }

  handleClick() {
    console.info('Hello from handleClick', this.props.data)
  }

  render() {
    const disabled = this.props.data.disabled === true
    return (
      <>
        <Button
          color="danger"
          onClick={this.handleClick}
          outline={false}
          type="button"
          className="btn btn-danger"
          disabled={disabled}
        >
          <svg
            className="bi bi-trash mr-1"
            width="1em"
            height="1em"
            viewBox="0 0 16 16"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
            <path
              fillRule="evenodd"
              d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"
            />
          </svg>
          {this.props.data.text}
        </Button>
      </>
    )
  }
}

DropButton.propTypes = propTypes
DropButton.defaultProps = defaultProps
export default DropButton

const dropButtons = document.getElementsByClassName('drop-button')

for (let i = 0, len = dropButtons.length | 0; i < len; i = (i + 1) | 0) {
  const dataset = dropButtons[i].dataset
  ReactDOM.render(<DropButton data={dataset} />, dropButtons[i])
}
