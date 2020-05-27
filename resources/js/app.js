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
const formKeyPress = (event, element = 'buttonElem') => {
  // Do Stuff
  console.log('boom')
  if (event.keyCode === 13) {
    element.click()
    return false
  }
}

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
    const CommitChangesButton = document.getElementById('commitChangesButton')
    const formFields = [
      document.getElementById('firstName'),
      document.getElementById('lastName'),
      document.getElementById('companyName'),
      document.getElementById('email'),
      document.getElementById('intake'),
      document.getElementById('phoneNumber'),
    ]
    const addInventoryItemButton = document.getElementById(
      'addInventoryItemButton'
    )
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
    const ableForm = (locked) => {
      const boolLocked = locked === 'true' || locked === true
      formFields.forEach((element) => {
        element.disabled = boolLocked
      })
    }
    // Do Stuff
    if (workOrderBody.dataset.isLocked === 'true') {
      updateUI(locked, unlocked)
      lockHeader.innerText = locked.lockHeader
    } else {
      updateUI(unlocked, locked)
      lockHeader.innerText = unlocked.lockHeader
    }
    if (addInventoryItemButton) {
      addInventoryItemButton.disabled =
        workOrderBody.dataset.isLocked === 'true'
    }
    ableForm(workOrderBody.dataset.isLocked)
    CommitChangesButton.disabled = workOrderBody.dataset.isLocked === 'true'
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
  const addNewProductModalShown = () => {
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
          productSubmit.disabled = false
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
        const { model, created_at: createdAt, serial } = response.data
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
        $('#addNewProductModal').modal('hide')
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

    // Do Stuff
    productSubmit.disabled = true
  }

  // Attachments
  $('#addNewProductModal').on('shown.bs.modal', (event) => {
    addNewProductModalShown(event)
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

  const clearButton = document.getElementById('clearButton')
  const loadFormButton = document.getElementById('loadButton')
  const previewButton = document.getElementById('previewButton')
  const productType = document.getElementById('productType')
  const saveFormButton = document.getElementById('saveFormButton')
  const $typesControlFormBuilder = $('#fb-editor').formBuilder(formOptions)
  const typesList = document.getElementById('typesList')

  // Actions
  const onTypesGet = (response) => {
    response.data.forEach((item) => {
      const option = document.createElement('option')
      const { slug, name } = item
      option.value = slug
      option.innerText = name
      typesList.append(option)
    })
  }
  const fetchTypesList = () => {
    loadFormButton.disabled = true
    loadFormButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
    while (typesList.hasChildNodes()) {
      typesList.removeChild(typesList.lastChild)
    }
    console.info('removed exiting typesList')
    axios
      .get('/types')
      .then((response) => {
        onTypesGet(response)
      })
      .finally(() => {
        loadFormButton.disabled = false
        loadFormButton.innerHTML = `Load Existing&emsp;&emsp;<i class="fas fa-file-download"></i>`
      })
  }
  const typesControlToggleEdit = () => {
    // Definitions
    const formBuilder = document.getElementById('formbuilder')
    const previewButton = document.getElementById('previewButton')
    const editing = previewButton.dataset.showOnClick
    const classes = previewButton.classList
    let renderPreview

    // Do Stuff
    if (editing === 'preview') {
      previewButton.innerHTML = 'Edit&emsp;&emsp;<i class="far fa-edit"></i>'
      previewButton.dataset.showOnClick = 'edit'

      classes.remove('btn-outline-secondary')
      classes.add('btn-secondary')
      renderPreview = false
    } else {
      previewButton.innerHTML = 'Preview&emsp;&emsp;<i class="far fa-eye"></i>'
      previewButton.dataset.showOnClick = 'preview'
      classes.add('btn-outline-secondary')
      classes.remove('btn-secondary')
      renderPreview = true
    }
    formBuilder.classList.toggle('form-rendered', !renderPreview)
  }
  const typesControlCheckAndClear = ($formBuilder) => {
    // Definitions
    const formData = $formBuilder.actions.getData('json', true)

    // Do Stuff
    if (formData !== '[]') {
      // Form exists
      if (window.confirm('Are you sure you want to clear all fields?')) {
        // $('#fb-render').formRender({ formData })
        previewButton.dataset.showOnClick = 'edit'
        previewButtonClick()
        $formBuilder.actions.clearFields()
        productType.innerText = ''
        return true
      }
      return false
    }
    return true
  }
  const previewButtonClick = () => {
    // Do Stuff
    typesControlToggleEdit()
    const formData = $typesControlFormBuilder.actions.getData('json', true)
    $('#fb-render').formRender({ formData })
  }
  const saveFormButtonClick = () => {
    console.info('pressed saveFormButton')
    // Definitions
    const formData = $typesControlFormBuilder.actions.getData('json', true)

    // Do Stuff
    if (formData === '[]') {
      // FormData is empty
      alert('Nothing to save!')
      return
    }
    $('#saveNewTypeModal').modal('show')
  }
  const saveNewTypeShown = () => {
    // Definitions
    const saveTypeButton = document.getElementById('saveTypeButton')
    const saveType = document.getElementById('saveType')
    const saveNewTypeAlerts = document.getElementById('saveNewTypeAlerts')

    // Actions
    const displaySaveNewTypeAlerts = () => {
      const classList = saveNewTypeAlerts.classList
      classList.add('d-block')
      classList.remove('d-none')
      saveTypeButton.disabled = true
    }
    const hideSaveNewTypeAlerts = () => {
      const classList = saveNewTypeAlerts.classList
      classList.remove('d-block')
      classList.add('d-none')
      saveTypeButton.disabled = false
    }
    const saveTypeButtonClick = () => {
      // Definitions
      console.info('Save button clicked.')
      const typeName = saveType.value
      const formData = $typesControlFormBuilder.actions.getData('json', true)
      const typesData = {
        form: formData,
        name: typeName,
      }
      const resaveTypesData = {
        force: true,
        ...typesData,
      }

      // Actions
      const afterTypesPost = () => {
        // Do Stuff
        $('#saveNewTypeModal').modal('hide')

        fetchTypesList()
        console.info('setting timeout?')
        window.setTimeout(() => {
          if (document.getElementById('alert')) {
            $('.alert').alert('close')
          }
        }, 8000)
      }
      const displaySuccessfulTypeSaveAlert = (response) => {
        console.info('created.')
        document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-success alert-dismissible fade show">
  <h5>Product Type Saved.</h5>
  You may now use ${response.data.name} as a product type.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
      }
      const displaySuccessfulTypeUpdateAlert = (response) => {
        console.info('forced created.')
        document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-info alert-dismissible fade show">
  <h5>Product Type Updated.</h5>
  ${response.data.name} has been updated. Existing products of this type have not been updated.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
      }
      const displayTypeSaveError = (statusCode) => {
        console.info('error', statusCode)
        document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-warning alert-dismissible fade show">
  <h5>Error Condition.</h5>
  ${typeName} has not been saved. An error code ${statusCode} was reported.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
      }
      const displayTypeUpdateCancelledAlert = () => {
        console.info('not force updating.')
        document.getElementById('alert').innerHTML = `
<div role="alert" class="alert alert-warning alert-dismissible fade show">
  <h5>Saving canceled.</h5>
  ${typeName} has not been saved. Please choose a different name for the product type.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>`
      }
      const displayUnableToUpdateTypeAlert = () => {
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

      // Do Stuff
      if (typeName === '') {
        displaySaveNewTypeAlerts()
        return
      }
      console.info('typeName', typeName)
      // This is the axios post that is getting cleaned up:
      axios
        .post('/types', typesData)
        .then((response) => {
          // onTypesPost
          if (response.status === HTTP_CREATED) {
            // New Type successfully Saved
            displaySuccessfulTypeSaveAlert(response)
          }
          return response
        })
        .then((response) => {
          if (response.status === HTTP_ACCEPTED) {
            // Existing Type. Ask User to force.
            console.info('accepted.')
            const forceOverwrite = window.confirm(
              'Type already exists. Press OK to update, CANCEL to rename'
            )
            if (forceOverwrite) {
              // Client wishes to force overwrite of existing type
              axios.post('/types', resaveTypesData).then((response) => {
                console.info('response', response)
                // onResavePost(response)
                if (response.status === HTTP_OK) {
                  // Successfully overwritten type
                  displaySuccessfulTypeUpdateAlert(response)
                } else {
                  // Server didn't say OK to overwrite
                  displayUnableToUpdateTypeAlert()
                  return response
                }
              })
            } else {
              // Client cancelled overwrite of existing type
              displayTypeUpdateCancelledAlert()
            }
          }
          return response
        })
        .then((response) => {
          if (
            response.status !== HTTP_OK &&
            response.status !== HTTP_ACCEPTED &&
            response.status !== HTTP_CREATED
          ) {
            displayTypeSaveError(response.status)
          }
        })
        .finally((response) => {
          afterTypesPost(response)
        })
    }
    const saveTypeChange = () => {
      if (saveType.value === '') {
        displaySaveNewTypeAlerts()
      } else {
        hideSaveNewTypeAlerts()
      }
    }

    // Attachments
    saveTypeButton.addEventListener('click', () => {
      saveTypeButtonClick()
    })
    saveType.addEventListener('change', () => {
      saveTypeChange()
    })
    saveType.addEventListener('keypress', (event) => {
      formKeyPress(event, saveTypeButton)
    })
    // Do Stuff
    hideSaveNewTypeAlerts()
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

        // Do Stuff
        typesControlToggleEdit()
        $('#fb-render').formRender({ formData })
        $typesControlFormBuilder.actions.setData(formData)
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
  const loadFormButtonClick = () => {
    // Do Stuff
    if (!typesControlCheckAndClear($typesControlFormBuilder)) {
      return
    }

    console.info('attempting to launch modal.')
    $('#loadProductModal').modal('show')
  }

  // Attachments
  previewButton.onclick = () => {
    previewButtonClick()
  }
  clearButton.onclick = () => {
    typesControlCheckAndClear($typesControlFormBuilder)
  }
  loadFormButton.onclick = () => {
    loadFormButtonClick()
  }
  saveFormButton.onclick = () => {
    saveFormButtonClick()
  }
  $('#saveNewTypeModal').on('shown.bs.modal', () => {
    saveNewTypeShown()
  })
  $('#loadProductModal').on('shown.bs.modal', () => {
    loadProductModalShown()
  })

  // Do Stuff
  $('#fb-render').formRender()
  fetchTypesList()
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
  dataCartMap.forEach(function (currentValue) {
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

    // Attachments
    costSubmitButton.addEventListener('click', () => {
      costSubmitButtonClick()
    })
    $('#form').bind('keypress', (event) => {
      formKeyPress(event, costSubmitButton)
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
