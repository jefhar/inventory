/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

import ReactDOM from 'react-dom'
import * as React from 'react'
import CompanyClientName from './components/WorkOrder/Elements/CompanyClientName'
import AutoComplete from 'autocomplete-js'

require('./bootstrap')
require('jquery-ui-sortable')
require('formBuilder')
require('formBuilder/dist/form-render.min')
require('./components/WorkOrder/WorkOrderIndex')
require('./components/WorkOrder/WorkOrderCreate')
const HTTP_OK = 200
const HTTP_CREATED = 201
const HTTP_ACCEPTED = 202

const CART_INVOICED = 'invoiced'
const CART_VOID = 'void'

AutoComplete()

$(() => {
  AutoComplete(
    {
      EmptyMessage: 'No results found',
      HttpHeaders: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      MinChars: 2,
      Url: '/ajaxsearch',
      _RenderResponseItems: function (response) {
        const ul = document.createElement('ul')

        let limit = this._Limit()
        if (limit < 0) {
          response = response.reverse()
        } else if (limit === 0) {
          limit = response.length
        }

        // This is where this implementation is different:
        for (
          let item = 0;
          item < Math.min(Math.abs(limit), response.length);
          item++
        ) {
          const li = document.createElement('li')
          const a = document.createElement('a')
          a.href = response[item].Url
          a.innerText = response[item].Label
          li.appendChild(a)
          li.setAttribute('data-autocomplete-value', response[item].Value)
          ul.appendChild(li)
        }
        return ul
      },
      _Post: function (response) {
        const json = JSON.parse(response)
        const returnResponse = []
        if (Array.isArray(json)) {
          console.info('json is array')
          for (let i = 0; i < Object.keys(json).length; i++) {
            console.info(json[i])

            returnResponse[returnResponse.length] = {
              Value: json[i].name,
              Label: json[i].name,
              Url: json[i].url,
            }
          }
        } else {
          console.info('json is not array')
          console.info(JSON.stringify(json))
        }
        return returnResponse
      },
    },
    '#site-search'
  )
})

function WorkOrderEditUpdateUI() {
  console.log('WorkOrderEditUpdateUI function')
  const inventoryButton = document.getElementById('addInventoryButton')
  const isLockedButton = document.getElementById('lockButton')
  const lockIcon = document.getElementById('lockIcon')
  const outline = document.getElementById('outline')
  const lockHeader = document.getElementById('lockedHeader')
  const workOrderBody = document.getElementById('workOrderBody')
  const locked = {
    clickTo: 'Click to Unlock work order.',
    isLockedButton: 'btn-outline-danger',
    lockHeader: 'Locked',
    lockIcon: 'fa-unlock-alt',
    outline: 'border-primary',
  }
  const unlocked = {
    clickTo: 'Click to Lock work order.',
    isLockedButton: 'btn-outline-success',
    lockHeader: 'Unlocked',
    lockIcon: 'fa-lock',
    outline: 'border-warning',
  }
  const updateUI = (add, remove) => {
    if (isLockedButton) {
      isLockedButton.classList.add(add.isLockedButton)
      lockIcon.classList.add(add.lockIcon)
    }
    outline.classList.add(add.outline)

    if (isLockedButton) {
      isLockedButton.classList.remove(remove.isLockedButton)
      lockIcon.classList.remove(remove.lockIcon)
    }
    outline.classList.remove(remove.outline)

    $('[data-toggle="tooltip"]').attr('data-original-title', add.clickTo)
  }

  if (workOrderBody.dataset.isLocked === 'true') {
    updateUI(locked, unlocked)
    lockHeader.innerText = locked.lockHeader
  } else {
    updateUI(unlocked, locked)
    lockHeader.innerText = unlocked.lockHeader
  }
  if (inventoryButton) {
    inventoryButton.disabled = workOrderBody.dataset.isLocked === 'true'
  }
}

if (document.getElementById('WorkOrdersEdit')) {
  console.info('WorkOrdersEdit')
  const lockButton = document.getElementById('lockButton')
  const updateButton = document.getElementById('updateButton')
  const workOrderBody = document.getElementById('workOrderBody')

  const lockButtonClick = () => {
    const wantOrderToBeLocked = !(workOrderBody.dataset.isLocked === 'true')
    const data = { is_locked: wantOrderToBeLocked }
    const url = `/workorders/${workOrderBody.dataset.workOrderId}`
    axios
      .patch(url, data)
      .then((response) => {
        workOrderBody.dataset.isLocked = response.data.is_locked
        WorkOrderEditUpdateUI()
      })
      .catch((error) => {
        console.info('error.response.data:', error.response.data)
      })
  }
  const updateButtonClick = (event) => {
    const cardBody = document.getElementById('workOrderBody')
    const updateToast = document.createElement('div')
    const url = `/workorders/${cardBody.dataset.workOrderId}`
    const data = {
      company_name: document.getElementById('company_name').value,
      email: document.getElementById('email').value,
      first_name: document.getElementById('first_name').value,
      intake: document.getElementById('intake').value,
      last_name: document.getElementById('last_name').value,
      phone_number: document.getElementById('phone_number').value,
    }

    updateToast.id = 'updateToast'
    updateToast.classList.add('toast')
    updateToast.style.position = 'absolute'
    updateToast.style.top = '0'
    updateToast.style.right = '0'
    updateToast.dataset.delay = '8000'

    // send PATCH via axios
    axios
      .patch(url, data)
      .then((response) => {
        // At some point, give user a visual indication that
        // fields have been updated. Even better, add onChange
        // to the fields, and if they're dirty, give visual
        // indication when changed.

        updateToast.innerHTML = ` 
<div class="toast-header">
  <i class="fas fa-check-square text-success"></i>&nbsp;
  <strong class="mr-auto">Success</strong>
  <button type="button" class="ml-2 mb-1 close" data-dismiss="toast"
          aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="toast-body">
  Work Order successfully updated.
</div>`
      })
      .catch((error) => {
        console.info('error:', error)
        updateToast.innerHTML = `
<div
  style="position: absolute; top: 0; right: 0;"
  class="toast"
  role="alert"
  aria-live="assertive" 
  aria-atomic="true">
  <div class="toast-header">
    <i class="fas fa-exclamation-circle text-warning"></i>&nbsp;
    <strong class="mr-auto">Warning</strong>
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast"
            aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">
    ${error}
  </div>
</div>`
      })
      .finally(() => {
        document.getElementById('workOrderBody').appendChild(updateToast)
        const $updateToast = $('#updateToast')
        $updateToast.toast()
        $updateToast.toast('show')
      })

    event.preventDefault()
  }
  const productModalShown = (event) => {
    // Identifiers
    const productType = document.getElementById('productType')
    const productSubmit = document.getElementById('productSubmit')
    const cancelButton = document.getElementById('cancelButton')

    // Attributes
    const productTypeChange = (event) => {
      const { value } = event.target
      const select = document.getElementById('productType')
      const $formContainer = $(document.getElementById('typeForm'))
      const spinner = document.getElementById('spinner')
      spinner.classList.remove('invisible')
      spinner.classList.add('visible')

      axios
        .get(`/types/${value}`, value)
        .then((response) => {
          const formData = response.data
          formData.unshift(
            {
              type: 'header',
              className: 'mt-3',
              label: select.options[select.selectedIndex].innerText,
              subtype: 'h3',
            },
            {
              className: 'form-control',
              dataAutocomplete: '/ajaxsearch/manufacturer',
              label: 'Manufacturer',
              name: 'manufacturer',
              required: 'true',
              subtype: 'text',
              type: 'text',
            },
            {
              className: 'form-control',
              dataAutocomplete: '/ajaxsearch/model',
              label: 'Model',
              name: 'model',
              required: 'true',
              subtype: 'text',
              type: 'text',
            }
          )

          $('form', $formContainer).formRender({
            formData: formData,
          })
          // Add autocomplete to Manufacturer
          AutoComplete({
            _Cache: function (value) {
              value += this.Input.name
              return this.$Cache[value]
            },
          })
          // Add autocomplete to Model
        })
        .catch((error) => {
          console.info('error:', error)
          const alert = document.createElement('div')
          alert.classList.add('alert', 'alert-warning')
          alert.innerText = error
          $formContainer.append(alert)
        })
        .finally(() => {
          spinner.classList.remove('visible')
          spinner.classList.add('invisible')
        })
    }
    const productSubmitClick = (event) => {
      const formData = document.getElementById('productForm')
      const postData = {
        type: document.getElementById('productType').value,
        workOrderId: document.getElementById('workOrderBody').dataset
          .workOrderId,
      }
      const url = '/products'
      for (let i = 0; i < formData.length; i++) {
        postData[formData[i].name] = formData[i].value
      }

      // Post Form
      axios
        .post(url, postData)
        .then((response) => {
          // Define stuff
          const { model, createdAt, serial } = response.data
          const { name: manufacturer } = response.data.manufacturer
          const { name: type } = response.data.type
          const luhn = _.padStart(response.data.luhn, 6, '0')
          const productForm = document.getElementById('productForm')
          // Add Row to `<tbody id="products_table">`
          const tr = document.createElement('tr')

          // Do stuff
          tr.innerHTML = `<th scope="row" class="col-1">
<a class="btn btn-info" href="/inventory/${luhn}">${luhn}</a></th>
<td>${manufacturer}</td>
<td>${model}</td>
<td>${type}</td>
<td>${serial}</td>
<td>${createdAt}</td>`
          document.getElementById('products_table').appendChild(tr)

          while (productForm.hasChildNodes()) {
            productForm.removeChild(productForm.lastChild)
          }
          document.getElementById('productType').selectedIndex = 0
          // close modal
          $('#productModal').modal('hide')
          $('.modal-backdrop').remove()
        })
        .catch((error) => {
          console.info('error:', error)
          const errorAlert = document.createElement('div')
          errorAlert.classList.add(
            'alert',
            'alert-warning',
            'alert-dismissible',
            'fade',
            'show'
          )
          errorAlert.innerHTML = `${error}
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>`
          document.getElementById('productError').appendChild(errorAlert)
        })
    }
    const cancelButtonClick = (event) => {
      const productForm = document.getElementById('productForm')
      while (productForm.hasChildNodes()) {
        productForm.removeChild(productForm.lastChild)
      }
      document.getElementById('productType').selectedIndex = 0
    }

    // Event Listeners
    // Defer attaching event listener until modal opens
    // Because #productType is not attached until modal opens
    productType.addEventListener('change', (event) => {
      productTypeChange(event)
    })
    productSubmit.addEventListener('click', (event) => {
      productSubmitClick(event)
    })
    cancelButton.addEventListener('click', (event) => {
      cancelButtonClick(event)
    })
  }

  $('[data-toggle="tooltip"]').tooltip()
  $('#productModal').on('shown.bs.modal', (event) => {
    productModalShown(event)
  })

  // Lock/Unlock work order
  if (lockButton) {
    lockButton.addEventListener('click', () => {
      lockButtonClick()
    })
  }
  // Submit changed field data to UPDATE
  if (updateButton) {
    updateButton.addEventListener('click', (event) => {
      updateButtonClick()
    })
  }

  WorkOrderEditUpdateUI()
}

if (document.getElementById('typesCreate')) {
  // Definitions
  console.info('typesCreate')
  const formOptions = {
    controlOrder: [
      'text',
      'number',
      'select',
      'checkbox-group',
      'radio-group',
      'date',
      'textarea',
    ],
    dataType: 'json',
    disabledSubtypes: {
      text: ['password', 'color', 'email'],
    },
    disableFields: [
      'autocomplete',
      'button',
      'file',
      'header',
      'hidden',
      'paragraph',
      'starRating',
    ],
    fieldRemoveWarn: true,
    onSave: (event, formData) => {
      typesControlToggleEdit(false)
      $('#fb-render').formRender({ formData })
      console.info(JSON.stringify(formData))
    },
    showActionButtons: false,
  }
  const typesControlFormBuilder = $('#fb-editor').formBuilder(formOptions)

  // Actions
  const typesControlToggleEdit = () => {
    // Definitions
    const formBuilder = document.getElementById('formbuilder')
    const previewButton = document.getElementById('previewButton')
    let renderPreview

    const editing = previewButton.dataset.showOnClick

    // Do Stuff
    if (editing === 'preview') {
      previewButton.innerHTML = 'Edit&emsp;&emsp;<i class="far fa-edit"></i>'
      previewButton.dataset.showOnClick = 'edit'
      renderPreview = false
    } else {
      previewButton.innerHTML = 'Preview&emsp;&emsp;<i class="far fa-eye"></i>'
      previewButton.dataset.showOnClick = 'preview'
      renderPreview = true
    }
    formBuilder.classList.toggle('form-rendered', !renderPreview)
  }
  const typesControlCheckAndClear = (formBuilder) => {
    const formData = formBuilder.actions.getData('json', true)

    if (formData !== '[]') {
      // Form exists
      if (window.confirm('Are you sure you want to clear all fields?')) {
        formBuilder.actions.clearFields()
        $('#fb-render').formRender({ formData })
        return true
      }
      return false
    }
    return true
  }
  const previewButtonClick = () => {
    typesControlToggleEdit()
    const formData = typesControlFormBuilder.actions.getData('json', true)
    $('#fb-render').formRender({ formData })
  }
  const saveButtonClick = () => {
    const formData = typesControlFormBuilder.actions.getData('json', true)
    if (formData === '[]') {
      // FormData is empty
      alert('Nothing to save!')
      return
    }

    // open save dialog modal
    $('#saveProductModal').modal('show')
  }
  const saveProductModalShown = (event) => {
    const saveTypeButton = document.getElementById('saveTypeButton')
    const saveTypeButtonClick = (event) => {
      console.info('Save button clicked.')
      const typeName = document.getElementById('saveType').value
      const formData = typesControlFormBuilder.actions.getData('json', true)

      axios
        .post('/types', {
          form: formData,
          name: typeName,
        })
        .then((response) => {
          if (response.status === HTTP_CREATED) {
            console.info('created.')
            document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-success alert-dismissible fade show">
  <h5>Product Type Saved.</h5>
  You may now use ${response.data.name} as a product type.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
          } else if (response.status === HTTP_ACCEPTED) {
            console.info('accepted.')
            const resave = window.confirm(
              'Type already exists. Press OK to update, CANCEL to rename'
            )
            if (resave) {
              axios
                .post('/types', {
                  force: true,
                  form: formData,
                  name: typeName,
                })
                .then((response) => {
                  if (response.status === HTTP_OK) {
                    console.info('forced created.')
                    document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-info alert-dismissible fade show">
  <h5>Product Type Updated.</h5>$
  {response.data.name} has been updated. Existing products of this type have not been updated.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
                  } else {
                    console.info('unable to force create.')
                    document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-warning alert-dismissible fade show">
  <h5>Saving failed.</h5>
  ${typeName} has not been updated. Existing products of this type have not been
  updated.
    <strong>An unexpected response was tendered from the server. Please try again later.</strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
                  }
                })
            } else {
              console.info('not force updating.')
              document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-warning alert-dismissible fade show">
  <h5>Saving canceled.</h5>$
  {typeName} has not been saved. Please choose a different name for the product type.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
            }
          } else {
            console.info(response)
            console.info('Hmm.')
          }
        })
        .finally((response) => {
          $('#saveProductModal').modal('hide')
          // Refresh typesList from server
          const typesList = document.getElementById('typesList')
          while (typesList.hasChildNodes()) {
            typesList.removeChild(typesList.lastChild)
          }

          axios.get('/types').then((response) => {
            response.data.forEach((item) => {
              const option = document.createElement('option')
              option.value = item.slug
              option.innerText = item.name
              typesList.append(option)
            })
          })
          console.info('setting timeout?')
          window.setTimeout(() => {
            if (document.getElementById('alert')) {
              $('.alert').alert('close')
            }
          }, 8000)
        })
    }
    saveTypeButton.addEventListener('click', (event) => {
      saveTypeButtonClick()
    })
  }
  const loadProductModalShown = (event) => {
    const loadTypeButton = document.getElementById('loadTypeButton')
    const loadTypeButtonClick = (event) => {
      // Save selected slug

      // Definitions
      const typesList = document.getElementById('typesList')
      const index = typesList.selectedIndex
      const value = document.getElementById('typesList').value
      const spinner = document.getElementById('spinner')
      const formBuilder = document.getElementById('formbuilder')

      // Actions
      const elementVisibility = (element, visible) => {
        if (visible) {
          element.classList.add('visible')
          element.classList.remove('invisible')
        } else {
          element.classList.add('invisible')
          element.classList.remove('visible')
        }
      }

      // Do Stuff
      $('#loadProductModal').modal('hide')
      elementVisibility(spinner, true)
      elementVisibility(formBuilder, false)

      axios
        .get(`/types/${value}`, value)
        .then((response) => {
          const formData = response.data
          const productType = document.getElementById('productType')

          typesControlToggleEdit()
          $('#fb-render').formRender({ formData })
          typesControlFormBuilder.actions.setData(formData)
          productType.innerHTML = `<h5>${typesList[index].label}</h5>`
        })
        .catch((error) => {
          console.info('error', error)
          const alert = document.createElement('div')
          alert.classList.add('alert', 'alert-warning')
          alert.innerText = error
          formBuilder.append(alert)
        })
        .finally(() => {
          elementVisibility(spinner, false)
          elementVisibility(formBuilder, true)
        })
    }

    loadTypeButton.addEventListener('click', (event) => {
      loadTypeButtonClick(event)
    })
  }
  const loadButtonClick = () => {
    if (!typesControlCheckAndClear(typesControlFormBuilder)) {
      return
    }

    console.info('attempting to launch modal.')
    $('#loadProductModal').modal('show')
  }

  // Attachments
  document.getElementById('previewButton').onclick = () => {
    previewButtonClick()
  }
  document.getElementById('clearButton').onclick = () => {
    typesControlCheckAndClear(typesControlFormBuilder)
  }
  document.getElementById('loadButton').onclick = () => {
    loadButtonClick()
  }
  document.getElementById('saveButton').onclick = () => {
    saveButtonClick()
  }

  $('#saveProductModal').on('shown.bs.modal', (event) => {
    saveProductModalShown()
  })
  $('#loadProductModal').on('shown.bs.modal', (event) => {
    loadProductModalShown()
  })
  $('#fb-render').formRender()
}

if (document.getElementById('inventory_show')) {
  function addToExistingCart(cartId, productId) {
    console.info('inside const addToCart(' + cartId + ', ' + productId + ')')
    axios
      .post('/pendingSales', {
        cart_id: cartId,
        id: productId,
      })
      .then((response) => {
        const { luhn: cartLuhn } = response.data.cart
        const { company_name: companyName } = response.data.cart.client
        document.getElementById('addToCardButton').remove()
        const alert = document.createElement('div')
        alert.id = 'productAddedAlert'
        alert.classList.add('alert', 'alert-primary')
        alert.innerHTML = `Product added to cart for <a href="/carts/${cartLuhn}">${companyName}</a>.
<br><br>
<span class="text-danger" id="removeFromCartIcon"><i id="removeProduct" class="fas fa-unlink" >&#8203;</i> Remove product from cart.</span>`
        document.getElementById('cardFooter').appendChild(alert)

        document
          .getElementById('removeFromCartIcon')
          .addEventListener(
            'click',
            removeFromCart.bind(
              this,
              document.getElementById('productId').dataset.productLuhn
            )
          )
      })
      .catch((error) => {
        console.info('error: ', error)
      })
  }

  const productId = document.getElementById('productId').dataset.productId
  const productLuhn = document.getElementById('productId').dataset.productLuhn
  document
    .querySelectorAll('[data-cart-id]')
    .forEach(function (currentValue, currentIndex, listObj) {
      currentValue.addEventListener(
        'click',
        addToExistingCart.bind(this, currentValue.dataset.cartId, productId)
      )
    })

  console.info('inventory page.')
  const wrapper = $('#product_show')
  wrapper.formRender(window.formRenderOptions)
  console.info(window.formRenderOptions)
  const $newCartModal = $('#newCartModal')

  if (document.getElementById('newCartButton')) {
    document.getElementById('newCartButton').onclick = () => {
      // Show popup modal
      $newCartModal.on('shown.bs.modal', (event) => {
        const handleResponse = function (response) {
          console.info('response.data:', response.data)
          const client = response.data.client
          const cartLuhn = response.data.luhn

          // Remove button
          const addToCardButton = document.getElementById('addToCardButton')
          const cardFooter = document.getElementById('cardFooter')
          addToCardButton.remove()
          const alert = document.createElement('div')
          alert.id = 'productAddedAlert'
          alert.classList.add('alert', 'alert-primary')
          alert.innerHTML = `Product added to cart for <a href="/carts/${cartLuhn}">${client.company_name}</a>.
<br><br>
<span class="text-danger" id="removeFromCartIcon"><i id="removeProduct" class="fas fa-unlink" >&#8203;</i> Remove product from cart.</span>`
          cardFooter.appendChild(alert)
          document
            .getElementById('removeFromCartIcon')
            .addEventListener(
              'click',
              removeFromCart.bind(
                this,
                document.getElementById('productId').dataset.productLuhn
              )
            )
          $newCartModal.modal('hide')
          return Promise.resolve()
        }
        console.info('rendering CCN for inventory')
        ReactDOM.render(
          <CompanyClientName
            handleResponse={handleResponse}
            postPath="/carts"
            draft="Cart"
          />,
          document.getElementById('carts_create')
        )
        document.getElementById('newCartButton').onclick = () => {
          console.info('Here.')
        }
      })
      $newCartModal.modal('show')
      console.info('got carts_create')
    }
  }
}

/** Don't refactor above this line; refactoring in other branch
 * @TODO: remove these 2 comments.
 */
if (document.getElementById('cartIndex')) {
  $('.collapse').collapse({ toggle: false })
  $('#destroyCartModal').on('show.bs.modal', (event) => {
    console.info('event:', event)
    const sourceTarget = $(event.relatedTarget)
    console.info('target:', sourceTarget)
    const cart = sourceTarget.data('cart')
    console.info('cart:', cart)

    document
      .getElementById('destroyCartButton')
      .addEventListener('click', () => {
        // Send destroy to /carts/destroy with cart luhn in field.
        axios.delete(`/carts/${cart}`)

        // Close modal
        $('#destroyCartModal').modal('hide')
        // Remove cart card from page
        document.getElementById(`cart${cart}`).remove()

        // Add toast
        document.getElementById('toastBody').innerText =
          'Cart ' +
          cart +
          ' has been destroyed. All items have been returned to inventory.'
        const $destroyedToast = $('#destroyedToast')
        $destroyedToast.toast()
        $destroyedToast.toast('show')
      })
  })
}

if (document.getElementById('cartShow')) {
  // elements
  const $costModal = $('#productCostModal')
  const editIcons = document.getElementsByClassName('fa-edit')
  const invoiceButton = document.getElementById('invoiceButton')
  const destroyButton = document.getElementById('destroyButton')

  // methods
  const changeInvoiceStatus = (status) => {
    const cartLuhn = document.getElementById('cartId').dataset.cartLuhn
    axios
      .patch(`/carts/${cartLuhn}`, { status: status })
      .then((result) => {
        console.info('result', result)
        const cardBorder = document.getElementById('card-border')
        console.info('cardBorder', cardBorder)
        const classes = cardBorder.classList
        classes.remove('border-secondary')
        classes.add(`border-${status === CART_INVOICED ? 'success' : 'danger'}`)
        document.getElementById('cartStatus').innerHTML = status
      })
      .catch((error) => {
        console.info('error', error)
      })
  }

  const removeEditIcons = () => {
    for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
      editIcons[i].remove()
    }
  }

  const updateTotalPrice = () => {
    let totalPrice = 0
    for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
      let price = editIcons[i].dataset.productPrice
      price = price.replace(/[^\d.-]/g, '')
      totalPrice += parseFloat(price, 10) * 100
    }
    document.getElementById('cartTotalPrice').innerText = new Intl.NumberFormat(
      'en-US',
      {
        style: 'currency',
        currency: 'USD',
      }
    ).format(totalPrice / 100)
  }

  const productCostPopup = (dataset) => {
    const {
      productLuhn: luhn,
      productManufacturer: manufacturer,
      productModel: model,
      productPrice: price,
    } = dataset
    document.getElementById('originalPrice').innerText = new Intl.NumberFormat(
      'en-US',
      {
        style: 'currency',
        currency: 'USD',
      }
    ).format(price)

    document.getElementById(
      'modalProductLuhn'
    ).innerText = `${luhn} ${manufacturer} ${model}`
    document.getElementById('modalProductLuhn').dataset.productLuhn = luhn

    $costModal.modal('show')
  }

  // events
  $costModal.on('shown.bs.modal', (event) => {
    $('#productPrice').trigger('focus')

    document
      .getElementById('costSubmitButton')
      .addEventListener('click', () => {
        const luhn = document.getElementById('modalProductLuhn').dataset
          .productLuhn
        const price = document.getElementById('productPrice').value
        if (price < 0) {
          return false
        }
        axios
          .patch(`/products/${luhn}`, { price: price })
          .then((response) => {
            $costModal.modal('hide')
            const $toast = $('#productPriceToast')
            document.getElementById('toastBody').innerHTML =
              `Product ${luhn} has been updated. ` +
              `The price is now $${response.data.price}. ` +
              `This price will remain even if the product is removed from this cart.`
            const priceId = document.getElementById(`price${luhn}`)
            priceId.innerText = new Intl.NumberFormat('en-US', {
              style: 'currency',
              currency: 'USD',
            }).format(response.data.price)
            const editElement = priceId.nextElementSibling
            editElement.dataset.productPrice = response.data.price
            console.info('editElement', editElement)
            console.info('productPrice', response.data.price)

            $toast.toast()
            $toast.toast('show')
          })
          .catch((error) => {
            console.error(error)
            // display error toast
            const $toast = $('productUpdateErrorToast')
            document.getElementById('toastErrorBody').innerHTML =
              `There was an error updating the price of product ${luhn}.` +
              `<br>${error}`
            $toast.toast()
            $toast.toast('show')
          })
          .finally(() => {
            updateTotalPrice()
          })
      })

    $('#form').bind('keypress', function (event) {
      if (event.keyCode === 13) {
        document.getElementById('costSubmitButton').click()
        return false
      }
    })
  })

  for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
    editIcons[i].addEventListener('click', () => {
      productCostPopup(editIcons[i].dataset)
    })
  }

  invoiceButton.addEventListener('click', function () {
    invoiceButton.disabled = true
    destroyButton.disabled = true
    changeInvoiceStatus(CART_INVOICED)
    removeEditIcons()
  })

  destroyButton.addEventListener('click', function () {
    invoiceButton.disabled = true
    destroyButton.disabled = true
    changeInvoiceStatus(CART_VOID)
    document.getElementById('cartTableBody').remove()
    document.getElementById('cartTotalPrice').innerText = '$0.00'
  })

  // Things to do on page load
  updateTotalPrice()
}
