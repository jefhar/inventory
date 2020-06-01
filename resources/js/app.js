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
require('./components/Dashboard')
require('./components/WorkOrder/WorkOrderCreate')
require('./components/WorkOrder/WorkOrderIndex')
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

if (document.getElementById('WorkOrdersEdit')) {
  // Definitions
  console.info('WorkOrdersEdit')
  const lockButton = document.getElementById('lockButton')
  const updateButton = document.getElementById('updateButton')
  const workOrderBody = document.getElementById('workOrderBody')

  // Actions
  const WorkOrderEditUpdateUI = () => {
    // Definitions
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

    // Actions
    const updateUI = (add, remove) => {
      // Do Stuff
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

    // Do Stuff
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
  const lockButtonClick = () => {
    // Definitions
    const wantOrderToBeLocked = !(workOrderBody.dataset.isLocked === 'true')
    const data = { is_locked: wantOrderToBeLocked }
    const url = `/workorders/${workOrderBody.dataset.workOrderId}`

    // Do Stuff
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
    // Definitions
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

    // Actions
    const onPatch = () => {
      // Do Stuff

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
    }
    const onPatchError = (error) => {
      // Do Stuff
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
    }
    const afterPatch = () => {
      // Do Stuff
      document.getElementById('workOrderBody').appendChild(updateToast)
      const $updateToast = $('#updateToast') // Grab jQuery handle element
      $updateToast.toast()
      $updateToast.toast('show')
    }
    // Do Stuff
    updateToast.id = 'updateToast'
    updateToast.classList.add('toast')
    updateToast.style.position = 'absolute'
    updateToast.style.top = '0'
    updateToast.style.right = '0'
    updateToast.dataset.delay = '8000'

    // send PATCH via axios
    axios
      .patch(url, data)
      .then(() => {
        onPatch()
      })
      .catch((error) => {
        onPatchError(error)
      })
      .finally(() => {
        afterPatch()
      })

    event.preventDefault()
  }
  const productModalShown = () => {
    // Definitions
    const productType = document.getElementById('productType')
    const productSubmit = document.getElementById('productSubmit')
    const cancelButton = document.getElementById('cancelButton')

    // Actions
    const productTypeChange = (event) => {
      // Definitions
      const { value } = event.target
      const select = document.getElementById('productType')
      const $formContainer = $(document.getElementById('typeForm'))
      const spinner = document.getElementById('spinner')

      // Actions
      const onGet = (response) => {
        // Definitions
        const formData = response.data

        // Do Stuff
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
      }
      const getError = (error) => {
        // Definitions
        console.info('error:', error)
        const alert = document.createElement('div')

        // Do Stuff
        alert.classList.add('alert', 'alert-warning')
        alert.innerText = error
        $formContainer.append(alert)
      }
      // Do Stuff
      spinner.classList.remove('invisible')
      spinner.classList.add('visible')

      axios
        .get(`/types/${value}`, value)
        .then((response) => {
          onGet(response)
        })
        .catch((error) => {
          getError(error)
        })
        .finally(() => {
          spinner.classList.remove('visible')
          spinner.classList.add('invisible')
        })
    }
    const productSubmitClick = () => {
      // Definitions
      const formData = document.getElementById('productForm')
      const postData = {
        type: document.getElementById('productType').value,
        workOrderId: document.getElementById('workOrderBody').dataset
          .workOrderId,
      }
      const url = '/products'

      // Actions
      const onPost = (response) => {
        // Definitions
        const { model, createdAt, serial } = response.data
        const { name: manufacturer } = response.data.manufacturer
        const { name: type } = response.data.type
        const luhn = _.padStart(response.data.luhn, 6, '0')
        const productForm = document.getElementById('productForm')
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
      }
      const postError = (error) => {
        // Definitions
        console.info('error:', error)
        const errorAlert = document.createElement('div')
        const productError = document.getElementById('productError')

        // Do Stuff
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
        productError.appendChild(errorAlert)
      }

      // Do Stuff
      for (let i = 0; i < formData.length; i++) {
        postData[formData[i].name] = formData[i].value
      }

      axios
        .post(url, postData)
        .then((response) => {
          onPost(response)
        })
        .catch((error) => {
          postError(error)
        })
    }
    const cancelButtonClick = () => {
      // Definitions
      const productForm = document.getElementById('productForm')
      const productType = document.getElementById('productType')

      // Do Stuff
      while (productForm.hasChildNodes()) {
        productForm.removeChild(productForm.lastChild)
      }
      productType.selectedIndex = 0
    }

    // Attachments
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

  // Attachments
  $('#productModal').on('shown.bs.modal', (event) => {
    productModalShown(event)
  })
  if (lockButton) {
    lockButton.addEventListener('click', () => {
      lockButtonClick()
    })
  }
  if (updateButton) {
    updateButton.addEventListener('click', () => {
      updateButtonClick()
    })
  }

  // Do Stuff
  $('[data-toggle="tooltip"]').tooltip()
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
    // Definitions
    const formData = formBuilder.actions.getData('json', true)

    // Do Stuff
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
    // Do Stuff
    typesControlToggleEdit()
    const formData = typesControlFormBuilder.actions.getData('json', true)
    $('#fb-render').formRender({ formData })
  }
  const saveButtonClick = () => {
    // Definitions
    const formData = typesControlFormBuilder.actions.getData('json', true)

    // Do Stuff
    if (formData === '[]') {
      // FormData is empty
      alert('Nothing to save!')
      return
    }
    $('#saveProductModal').modal('show')
  }
  const saveProductModalShown = () => {
    // Definitions
    const saveTypeButton = document.getElementById('saveTypeButton')

    // Actions
    const saveTypeButtonClick = () => {
      // Definitions
      console.info('Save button clicked.')
      const typeName = document.getElementById('saveType').value
      const formData = typesControlFormBuilder.actions.getData('json', true)
      const typesData = {
        form: formData,
        name: typeName,
      }

      // Actions
      const onTypesPost = (response) => {
        // Definitions
        const resaveTypesData = {
          force: true,
          form: formData,
          name: typeName,
        }

        // Actions
        const onResavePost = (response) => {
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
        }

        // Do Stuff
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
            axios.post('/types', resaveTypesData).then((response) => {
              onResavePost(response)
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
      }
      const afterTypesPost = () => {
        // Definitions
        const typesList = document.getElementById('typesList')

        // Actions
        const onTypesGet = (response) => {
          response.data.forEach((item) => {
            const option = document.createElement('option')
            option.value = item.slug
            option.innerText = item.name
            typesList.append(option)
          })
        }

        // Do Stuff
        $('#saveProductModal').modal('hide')

        // Refresh typesList from server
        while (typesList.hasChildNodes()) {
          typesList.removeChild(typesList.lastChild)
        }
        axios.get('/types').then((response) => {
          onTypesGet(response)
        })

        console.info('setting timeout?')
        window.setTimeout(() => {
          if (document.getElementById('alert')) {
            $('.alert').alert('close')
          }
        }, 8000)
      }

      // Do Stuff
      axios
        .post('/types', typesData)
        .then((response) => {
          onTypesPost(response)
        })
        .finally((response) => {
          afterTypesPost(response)
        })
    }

    // Attachments
    saveTypeButton.addEventListener('click', () => {
      saveTypeButtonClick()
    })
  }
  const loadProductModalShown = () => {
    // Definitions
    const loadTypeButton = document.getElementById('loadTypeButton')

    // Actions
    const loadTypeButtonClick = () => {
      // Definitions
      const typesList = document.getElementById('typesList')
      const index = typesList.selectedIndex
      const value = document.getElementById('typesList').value
      const spinner = document.getElementById('spinner')
      const formBuilder = document.getElementById('formbuilder')

      // Actions
      const elementVisibility = (element, visible) => {
        // Do Stuff
        if (visible) {
          element.classList.add('visible')
          element.classList.remove('invisible')
        } else {
          element.classList.add('invisible')
          element.classList.remove('visible')
        }
      }
      const onTypeValueGet = (response) => {
        // Definitions
        const formData = response.data
        const productType = document.getElementById('productType')

        // Do Stuff
        typesControlToggleEdit()
        $('#fb-render').formRender({ formData })
        typesControlFormBuilder.actions.setData(formData)
        productType.innerHTML = `<h5>${typesList[index].label}</h5>`
      }
      const onTypeValueError = (error) => {
        // Definitions
        console.info('error', error)
        const alert = document.createElement('div')

        // Do Stuff
        alert.classList.add('alert', 'alert-warning')
        alert.innerText = error
        formBuilder.append(alert)
      }

      // Do Stuff
      $('#loadProductModal').modal('hide')
      elementVisibility(spinner, true)
      elementVisibility(formBuilder, false)

      axios
        .get(`/types/${value}`, value)
        .then((response) => {
          onTypeValueGet(response)
        })
        .catch((error) => {
          onTypeValueError(error)
        })
        .finally(() => {
          elementVisibility(spinner, false)
          elementVisibility(formBuilder, true)
        })
    }

    // Attachments
    loadTypeButton.addEventListener('click', (event) => {
      loadTypeButtonClick(event)
    })
  }
  const loadButtonClick = () => {
    // Do Stuff
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
  $('#saveProductModal').on('shown.bs.modal', () => {
    saveProductModalShown()
  })
  $('#loadProductModal').on('shown.bs.modal', () => {
    loadProductModalShown()
  })

  // Do Stuff
  $('#fb-render').formRender()
}

if (document.getElementById('inventoryShow')) {
  console.info('inventory page.')
  // Definitions
  const dataCartMap = document.querySelectorAll('[data-cart-id]')
  const $newCartModal = $('#newCartModal')
  const wrapper = $('#product_show')
  const newCartButton = document.getElementById('newCartButton')
  const productId = document.getElementById('productId').dataset.productId
  // let productLuhn = document.getElementById('productId').dataset.productLuhn

  // Actions
  const addToExistingCart = (cartId, productId) => {
    console.info(`inside const addToCart(${cartId}, ${productId})`)
    // Definitions
    const postData = {
      cart_id: cartId,
      id: productId,
    }

    // Actions
    const onPendingSalePost = (response) => {
      // Definitions
      const cardFooter = document.getElementById('cardFooter')
      const removeFromCartIcon = document.getElementById('removeFromCartIcon')
      const { luhn: cartLuhn } = response.data.cart
      const { company_name: companyName } = response.data.cart.client
      const alert = document.createElement('div')

      // Attachments
      removeFromCartIcon.addEventListener(
        'click',
        removeFromCart.bind(
          this,
          document.getElementById('productId').dataset.productLuhn
        )
      )

      // Do Stuff
      document.getElementById('addToCardButton').remove()
      alert.id = 'productAddedAlert'
      alert.classList.add('alert', 'alert-primary')
      alert.innerHTML = `
Product added to cart for <a href="/carts/${cartLuhn}">${companyName}</a>.
<br><br>
<span class="text-danger" id="removeFromCartIcon">
  <i id="removeProduct" class="fas fa-unlink" >&#8203;</i>
  Remove product from cart.</span>`

      cardFooter.appendChild(alert)
    }

    // Do Stuff
    axios
      .post('/pendingSales', postData)
      .then((response) => {
        onPendingSalePost(response)
      })
      .catch((error) => {
        console.info('error: ', error)
      })
  }
  const newCartButtonClick = () => {
    // Actions
    const newCartModalShown = () => {
      // Definitions

      // Actions
      const handleResponse = (response) => {
        // Definitions
        console.info('response.data:', response.data)
        const client = response.data.client
        const cartLuhn = response.data.luhn
        const addToCardButton = document.getElementById('addToCardButton')
        const cardFooter = document.getElementById('cardFooter')
        const alert = document.createElement('div')
        const removeFromCartIcon = document.getElementById('removeFromCartIcon')
        const productId = document.getElementById('productId')
        // Attachments
        removeFromCartIcon.addEventListener(
          'click',
          removeFromCart.bind(this, productId.dataset.productLuhn)
        )

        // Do Stuff
        addToCardButton.remove()
        alert.id = 'productAddedAlert'
        alert.classList.add('alert', 'alert-primary')
        alert.innerHTML = `Product added to cart for <a href="/carts/${cartLuhn}">${client.company_name}</a>.
<br><br>
<span class="text-danger" id="removeFromCartIcon"><i id="removeProduct" class="fas fa-unlink" >&#8203;</i> Remove product from cart.</span>`
        cardFooter.appendChild(alert)

        $newCartModal.modal('hide')
        return Promise.resolve()
      }

      // Attachments
      document.getElementById('newCartButton').onclick = () => {
        console.info('Here.')
      }

      // Do Stuff
      console.info('rendering CCN for inventory')
      ReactDOM.render(
        <CompanyClientName
          handleResponse={handleResponse}
          postPath="/carts"
          draft="Cart"
        />,
        document.getElementById('carts_create')
      )
    }

    // Attachments
    $newCartModal.on('shown.bs.modal', () => {
      newCartModalShown()
    })

    // Do Stuff
    $newCartModal.modal('show')
    console.info('got carts_create')
  }

  // Attachments
  dataCartMap.forEach(function (currentValue, currentIndex, listObj) {
    currentValue.addEventListener(
      'click',
      addToExistingCart.bind(this, currentValue.dataset.cartId, productId)
    )
  })

  if (newCartButton) {
    newCartButton.onclick = () => {
      newCartButtonClick()
    }
  }

  // Do Stuff
  wrapper.formRender(window.formRenderOptions)
  console.info(window.formRenderOptions)
}

if (document.getElementById('cartIndex')) {
  // Actions
  const destroyCartModalShow = (event) => {
    // Definitions
    console.info('event:', event)
    const sourceTarget = $(event.relatedTarget)
    console.info('target:', sourceTarget)
    const cart = sourceTarget.data('cart')
    console.info('cart:', cart)
    const destroyCartButton = document.getElementById('destroyCartButton')

    // Actions
    const destroyCartButtonClick = () => {
      // Definitions
      const toastBody = document.getElementById('toastBody')
      const $destroyedToast = $('#destroyedToast')

      // Actions
      const onDeleteCart = () => {
        // Do Stuff
        $('#destroyCartModal').modal('hide')
        document.getElementById(`cart${cart}`).remove()

        // Add toast
        toastBody.innerText = `Cart ${cart} has been destroyed. All items have been returned to inventory.`

        $destroyedToast.toast()
        $destroyedToast.toast('show')
      }

      // Do Stuff
      // Send destroy to /carts/destroy with cart luhn in field.
      axios.delete(`/carts/${cart}`).then(() => {
        onDeleteCart()
      })
    }

    // Attachments
    destroyCartButton.addEventListener('click', () => {
      destroyCartButtonClick()
    })
  }

  // Attachments
  $('#destroyCartModal').on('show.bs.modal', (event) => {
    destroyCartModalShow(event)
  })

  // Do Stuff
  $('.collapse').collapse({ toggle: false })
}

if (document.getElementById('cartShow')) {
  // Definitions
  const $costModal = $('#productCostModal')
  const editIcons = document.getElementsByClassName('fa-edit')
  const invoiceButton = document.getElementById('invoiceButton')
  const destroyButton = document.getElementById('destroyButton')

  // Actions
  const changeInvoiceStatus = (status) => {
    // Definitions
    const cartLuhn = document.getElementById('cartId').dataset.cartLuhn

    // Actions
    const onPatch = (result) => {
      // Definitions
      console.info('result', result)
      const cardBorder = document.getElementById('card-border')
      console.info('cardBorder', cardBorder)
      const classes = cardBorder.classList

      // Do Stuff
      classes.remove('border-secondary')
      classes.add(`border-${status === CART_INVOICED ? 'success' : 'danger'}`)
      document.getElementById('cartStatus').innerHTML = status
    }
    // Do Stuff
    axios
      .patch(`/carts/${cartLuhn}`, { status: status })
      .then((result) => {
        onPatch(result)
      })
      .catch((error) => {
        console.info('error', error)
      })
  }
  const removeEditIcons = () => {
    // Do Stuff
    for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
      editIcons[i].remove()
    }
  }
  const updateTotalPrice = () => {
    // Definitions
    let totalPrice = 0

    // Do Stuff
    for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
      let price = editIcons[i].dataset.productPrice
      price = price.replace(/[^\d.-]/g, '')
      totalPrice += parseFloat(price) * 100
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
    // Definitions
    const {
      productLuhn: luhn,
      productManufacturer: manufacturer,
      productModel: model,
      productPrice: price,
    } = dataset
    const modalProductLuhn = document.getElementById('modalProductLuhn')
    const originalPrice = document.getElementById('originalPrice')

    // Do Stuff
    originalPrice.innerText = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    }).format(price)

    modalProductLuhn.innerText = `${luhn} ${manufacturer} ${model}`
    modalProductLuhn.dataset.productLuhn = luhn
    $costModal.modal('show')
  }
  const invoiceButtonClick = () => {
    // Do Stuff
    invoiceButton.disabled = true
    destroyButton.disabled = true
    changeInvoiceStatus(CART_INVOICED)
    removeEditIcons()
  }
  const destroyButtonClick = () => {
    // Do Stuff
    invoiceButton.disabled = true
    destroyButton.disabled = true
    changeInvoiceStatus(CART_VOID)
    document.getElementById('cartTableBody').remove()
    document.getElementById('cartTotalPrice').innerText = '$0.00'
  }
  const costModalShown = () => {
    // Definitions
    const costSubmitButton = document.getElementById('costSubmitButton')

    // Actions
    const costSubmitButtonClick = () => {
      // Definitions
      const luhn = document.getElementById('modalProductLuhn').dataset
        .productLuhn
      const price = document.getElementById('productPrice').value
      const patchData = { price: price }

      // Actions
      const onPatch = (response) => {
        // Definitions
        const $toast = $('#productPriceToast')
        const priceId = document.getElementById(`price${luhn}`)
        const editElement = priceId.nextElementSibling

        // Do Stuff
        $costModal.modal('hide')
        document.getElementById('toastBody').innerHTML =
          `Product ${luhn} has been updated. ` +
          `The price is now $${response.data.price}. ` +
          `This price will remain even if the product is removed from this cart.`
        priceId.innerText = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
        }).format(response.data.price)

        editElement.dataset.productPrice = response.data.price
        console.info('editElement', editElement)
        console.info('productPrice', response.data.price)

        $toast.toast()
        $toast.toast('show')
      }
      const patchError = (error) => {
        // Definitions
        const $toast = $('productUpdateErrorToast')
        const toastErrorBody = document.getElementById('toastErrorBody')

        // Do Stuff
        console.error(error)
        // display error toast
        toastErrorBody.innerHTML = `There was an error updating the price of product ${luhn}.<br>${error}`
        $toast.toast()
        $toast.toast('show')
      }

      // Do stuff
      if (price < 0) {
        return false
      }
      axios
        .patch(`/products/${luhn}`, patchData)
        .then((response) => {
          onPatch(response)
        })
        .catch((error) => {
          patchError(error)
        })
        .finally(() => {
          updateTotalPrice()
        })
    }
    const formKeyPress = (event) => {
      // Do Stuff
      if (event.keyCode === 13) {
        document.getElementById('costSubmitButton').click()
        return false
      }
    }

    // Attachments
    costSubmitButton.addEventListener('click', () => {
      costSubmitButtonClick()
    })
    $('#form').bind('keypress', (event) => {
      formKeyPress(event)
    })

    // Do Stuff
    $('#productPrice').trigger('focus')
  }

  // Attachments
  $costModal.on('shown.bs.modal', () => {
    costModalShown()
  })
  for (let i = 0, len = editIcons.length | 0; i < len; i = (i + 1) | 0) {
    editIcons[i].addEventListener('click', () => {
      productCostPopup(editIcons[i].dataset)
    })
  }

  invoiceButton.addEventListener('click', function () {
    invoiceButtonClick()
  })

  destroyButton.addEventListener('click', function () {
    destroyButtonClick()
  })

  // Do Stuff
  updateTotalPrice()
}
