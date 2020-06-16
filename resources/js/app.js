/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

import ReactDOM from 'react-dom'
import * as React from 'react'
import CompanyClientName from './components/layouts/WorkOrder/Elements/CompanyClientName'
import AutoComplete from 'autocomplete-js'
// import DropButton from './components/DropButton'

require('./bootstrap')
require('jquery-ui-sortable')
require('formBuilder')
require('formBuilder/dist/form-render.min')
require('./components/layouts/Dashboard')
require('./components/layouts/WorkOrder/WorkOrderIndex')
require('./components/layouts/WorkOrder/WorkOrderCreate')
require('./components/layouts/CartShow')
const HTTP_OK = 200
const HTTP_CREATED = 201
const HTTP_ACCEPTED = 202

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
  const commitChangesButton = document.getElementById('commitChangesButton')
  const lockButton = document.getElementById('lockButton')
  const workOrderBody = document.getElementById('workOrderBody')

  // Actions
  const WorkOrderEditUpdateUI = () => {
    // Definitions
    console.log('WorkOrderEditUpdateUI function')
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
      outline: 'border-danger',
    }
    const unlocked = {
      clickTo: 'Click to Lock work order.',
      isLockedButton: 'btn-outline-success',
      lockHeader: 'Unlocked',
      lockIcon: 'fa-lock',
      outline: 'border-success',
    }

    // Actions
    const updateLockButtonView = (add, remove) => {
      // Do Stuff
      if (isLockedButton) {
        isLockedButton.classList.add(add.isLockedButton)
        isLockedButton.classList.remove(remove.isLockedButton)
        lockIcon.classList.add(add.lockIcon)
        lockIcon.classList.remove(remove.lockIcon)
      }
      outline.classList.add(add.outline)
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
      updateLockButtonView(locked, unlocked)
      lockHeader.innerText = locked.lockHeader
    } else {
      updateLockButtonView(unlocked, locked)
      lockHeader.innerText = unlocked.lockHeader
    }
    if (addInventoryItemButton) {
      addInventoryItemButton.disabled =
        workOrderBody.dataset.isLocked === 'true'
    }
    ableForm(workOrderBody.dataset.isLocked)
    commitChangesButton.disabled = workOrderBody.dataset.isLocked === 'true'
  }
  const storeChanges = (data) => {
    // Definitions
    const cardBody = document.getElementById('workOrderBody')
    const updateToast = document.createElement('div')
    const url = `/workorders/${cardBody.dataset.workOrderId}`

    // Actions
    const onPatch = (response) => {
      // Do Stuff
      if ('is_locked' in response.data) {
        workOrderBody.dataset.isLocked = response.data.is_locked
        updateToast.style.bottom = '0'
      } else {
        updateToast.style.top = '0'
      }

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
      updateToast.id = 'updateToast'
      updateToast.classList.add('toast')
      updateToast.style.position = 'absolute'
      updateToast.style.right = '0'
      updateToast.dataset.delay = '8000'

      document.getElementById('toastContainer').appendChild(updateToast)
      const $updateToast = $('#updateToast') // Grab jQuery handle element
      $updateToast.toast()
      $updateToast.toast('show')
    }

    // Do Stuff

    // send PATCH via axios
    axios
      .patch(url, data)
      .then((response) => {
        onPatch(response)
      })
      .catch((error) => {
        onPatchError(error)
      })
      .finally(() => {
        afterPatch()
        WorkOrderEditUpdateUI()
      })
  }
  const lockButtonClick = () => {
    // Definitions
    const wantOrderToBeLocked = !(workOrderBody.dataset.isLocked === 'true')
    const data = { is_locked: wantOrderToBeLocked }
    storeChanges(data)
  }
  const commitChangesClick = () => {
    storeChanges({
      client_company_name: document.getElementById('companyName').value,
      email: document.getElementById('email').value,
      first_name: document.getElementById('firstName').value,
      intake: document.getElementById('intake').value,
      last_name: document.getElementById('lastName').value,
      phone_number: document.getElementById('phoneNumber').value,
    })
  }
  const addNewProductModalShown = () => {
    // TODO: Incorporate fetchTypesList() here so typeslist is always refreshed.
    // Definitions
    const productType = document.getElementById('productType')
    const productSubmit = document.getElementById('productSubmit')
    const cancelNewProductButton = document.getElementById(
      'cancelNewProductButton'
    )

    // Actions
    const removeChildren = (element) => {
      while (element.hasChildNodes()) {
        element.removeChild(element.lastChild)
      }
    }
    const productTypeChange = (event) => {
      // Definitions
      const { value } = event.target
      const select = document.getElementById('productType')
      const $formContainer = $(document.getElementById('typeForm'))
      const spinner = document.getElementById('spinner')

      // Actions
      const onGetType = (response) => {
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
            name: 'manufacturer_name',
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
          onGetType(response)
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
        workorder_id: document.getElementById('workOrderBody').dataset
          .workOrderId,
      }
      const url = '/products'

      // Actions
      const onPost = (response) => {
        // Definitions
        const {
          created_at: createdAt,
          manufacturer_name: manufacturerName,
          model,
          serial,
        } = response.data
        const { name: type } = response.data.type
        const productId = _.padStart(response.data.product_id, 6, '0')
        const productForm = document.getElementById('productForm')
        const productsTable = document.getElementById('productsTable')
        const productType = document.getElementById('productType')
        const tr = document.createElement('tr')
        // Do stuff
        tr.innerHTML = `<th scope="row" class="col-1">
<a class="btn btn-info" href="/inventory/${productId}">${productId}</a></th>
<td>${manufacturerName}</td>
<td>${model}</td>
<td>${type}</td>
<td>${serial}</td>
<td>${createdAt}</td>`
        productsTable.appendChild(tr)

        // reset modal form:
        removeChildren(productForm)
        productType.selectedIndex = 0

        // Add Toast
        const addedProductToast = document.createElement('div')
        addedProductToast.innerHTML = `
<div class="toast-header">
  <i class="fas fa-check-square text-success"></i>&nbsp;
  <strong class="mr-auto">Success</strong>
  <button type="button" class="ml-2 mb-1 close" data-dismiss="toast"
          aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="toast-body">
Product ${productId} ${manufacturerName} ${model} ${type} has been added to Work
Order ${postData.workorder_id}.
</div>`
        addedProductToast.id = 'addedProductToast'
        addedProductToast.classList.add('toast')
        addedProductToast.style.position = 'absolute'
        addedProductToast.style.top = '0'
        addedProductToast.style.right = '0'
        addedProductToast.style.width = '80em'
        addedProductToast.dataset.delay = '8000'

        document.getElementById('toastContainer').appendChild(addedProductToast)
        const $addedProductToast = $('#addedProductToast') // Grab jQuery
        // handle element
        $addedProductToast.toast()
        $addedProductToast.toast('show')
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
      // reset modal form:
      removeChildren(productForm)
      productType.selectedIndex = 0
    }

    // Attachments
    productType.addEventListener('change', (event) => {
      productTypeChange(event)
    })
    productSubmit.addEventListener('click', (event) => {
      productSubmitClick(event)
    })
    cancelNewProductButton.addEventListener('click', (event) => {
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
  if (commitChangesButton) {
    commitChangesButton.addEventListener('click', () => {
      commitChangesClick()
    })
  }

  // Do Stuff
  $('[data-toggle="tooltip"]').tooltip()
  WorkOrderEditUpdateUI()
}

if (document.getElementById('typesCreate')) {
  // Definitions
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
    },
    showActionButtons: false,
  }
  const clearButton = document.getElementById('clearButton')
  const loadFormButton = document.getElementById('loadButton')
  const previewButton = document.getElementById('previewButton')
  const productType = document.getElementById('productType')
  const saveFormButton = document.getElementById('saveFormButton')
  const saveType = document.getElementById('saveType')
  const $typesControlFormBuilder = $('#fb-editor').formBuilder(formOptions)
  const typesList = document.getElementById('typesList')
  const $loadProductModal = $('#loadProductModal')

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
    axios
      .get('/types')
      .then((response) => {
        onTypesGet(response)
      })
      .finally(() => {
        loadFormButton.disabled = false
        loadFormButton.innerHTML = `<i class="fas fa-file-download mr-1"></i>Load Existing`
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
      previewButton.innerHTML =
        'Edit&emsp;&emsp;<i class="far fa-edit pr-1"></i>'
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
  const saveNewTypeModalShown = () => {
    // Definitions
    const saveTypeButton = document.getElementById('saveTypeButton')

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
        window.setTimeout(() => {
          if (document.getElementById('alert')) {
            $('.alert').alert('close')
          }
        }, 8000)
      }
      const displaySuccessfulTypeSaveAlert = (response) => {
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
      axios
        .post('/types', typesData)
        .then((response) => {
          if (response.status === HTTP_CREATED) {
            // New Type successfully Saved
            displaySuccessfulTypeSaveAlert(response)
          }
          return response
        })
        .then((response) => {
          if (response.status === HTTP_ACCEPTED) {
            // Existing Type. Ask User to force.
            const forceOverwrite = window.confirm(
              'Type already exists. Press OK to update, CANCEL to rename'
            )
            if (forceOverwrite) {
              // Client wishes to force overwrite of existing type
              axios.post('/types', resaveTypesData).then((response) => {
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
    saveType.trigger('focus')
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
      $loadProductModal.modal('hide')
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

    $loadProductModal.modal('show')
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
    saveNewTypeModalShown()
  })
  $loadProductModal.on('shown.bs.modal', () => {
    loadProductModalShown()
  })

  // Do Stuff
  $('#fb-render').formRender()
  fetchTypesList()
}

if (document.getElementById('inventoryShow')) {
  // @TODO: post updated product to InventoryController::UPDATE_NAME, $product

  // Definitions
  const $newCartModal = $('#newCartModal')
  const $productView = $('#productView')
  const addToCartButton = document.getElementById('addToCartButton')
  const cardFooter = document.getElementById('cardFooter')
  const dataCartMap = document.querySelectorAll('[data-cart-id]')
  const newCartButton = document.getElementById('newCartButton')
  const productId = document.getElementById('productId').dataset.productId
  const productInCartButton = document.getElementById('productInCartButton')

  // Actions
  const removeFromCart = (productId) => {
    axios.delete(`/pendingSales/${productId}`).then(() => {
      // modify alert
      const productAddedAlert = document.getElementById('productAddedAlert')
      productAddedAlert.classList.remove('alert-primary')
      productAddedAlert.classList.add('alert-warning')
      productAddedAlert.innerHTML = `Product removed from cart. <a href="${window.location.href}">Reload page</a> to add it to a cart.`
    })
  }
  const createProductAddedToCartAlert = (cartId, companyName) => {
    // Definitions
    const alert = document.createElement('div')
    // Do Stuff
    alert.id = 'productAddedAlert'
    alert.classList.add('alert', 'alert-primary')
    alert.innerHTML = `
Product added to cart for <a href="/carts/${cartId}">${companyName}</a>.<br>`
    return alert
  }
  const createRemoveFromCartButton = () => {
    const removeFromCartButton = document.createElement('button')
    removeFromCartButton.classList.add('btn', 'btn-outline-danger')
    removeFromCartButton.setAttribute('type', 'button')
    removeFromCartButton.dataset.productId = productId
    removeFromCartButton.innerHTML = `<i class="fas fa-trash-alt"></i> Remove Product From Cart`
    // Attachments
    removeFromCartButton.addEventListener('click', () => {
      removeFromCart(productId)
    })
    return removeFromCartButton
  }
  const handlePostResponse = (response) => {
    // Definitions
    const { cart_id: cartId, company_client_name: companyName } = response.data
    console.info('data:', {
      cart: cartId,
      ccn: companyName,
      data: response.data,
    })
    const alert = createProductAddedToCartAlert(cartId, companyName)
    const removeFromCartButton = createRemoveFromCartButton()
    alert.appendChild(removeFromCartButton)
    addToCartButton.remove()
    cardFooter.appendChild(alert)
  }
  const addToExistingCart = (cartId, productId) => {
    // Definitions
    const postData = {
      cart_id: cartId,
      product_id: productId,
    }
    // Do Stuff
    axios
      .post('/pendingSales', postData)
      .then((response) => {
        handlePostResponse(response)
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
        const {
          client_company_name: companyName,
          cart_id: cartId,
        } = response.data
        const cardFooter = document.getElementById('cardFooter')

        const alert = createProductAddedToCartAlert(cartId, companyName)

        // Do Stuff
        addToCartButton.remove()

        // Do Stuff

        cardFooter.appendChild(alert)

        $newCartModal.modal('hide')
        return Promise.resolve()
      }

      // Do Stuff
      ReactDOM.render(
        <CompanyClientName
          handleResponse={handleResponse}
          postPath="/carts"
          draft="Cart and Add Product"
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
  }

  // Attachments
  dataCartMap.forEach((currentValue) => {
    currentValue.addEventListener(
      'click',
      addToExistingCart.bind(this, currentValue.dataset.cartId, productId)
    )
  })

  // Only shown if user has UserPermissions::EDIT_SAVED_PRODUCT
  if (newCartButton) {
    newCartButton.onclick = () => {
      newCartButtonClick()
    }
  }

  if (productInCartButton) {
    productInCartButton.addEventListener('click', () => {
      removeFromCart(productId)
    })
  }

  // Do Stuff
  $productView.formRender(window.formRenderOptions)
}

if (document.getElementById('cartIndex')) {
  // Actions

  // Do Stuff
  $('.collapse').collapse({ toggle: false })
}

if (document.getElementById('cartShow')) {
  // Definitions
  const $costModal = $('#productCostModal')
  let activeProduct = {}
  const addPriceButtons = document.getElementsByClassName('price-button')
  const invoiceButton = document.getElementById('invoiceButton')
  const destroyCartButton = document.getElementById('destroyCartButton')

  // Actions
  const changeInvoiceStatus = (status) => {
    // Definitions
    const cartId = document.getElementById('cartId').dataset.cartId

    // Actions
    const onPatch = () => {
      // Definitions
      const cardBorder = document.getElementById('card-border')
      const classes = cardBorder.classList

      // Do Stuff
      classes.remove('border-secondary')
      classes.add(
        `border-${status === DropButton.CART_INVOICED ? 'success' : 'danger'}`
      )
      document.getElementById('cartStatus').innerHTML = status
    }
    // Do Stuff
    axios
      .patch(`/carts/${cartId}`, { status: status })
      .then((result) => {
        onPatch(result)
      })
      .catch((error) => {
        console.info('error', error)
      })
  }
  const disablePriceButtons = () => {
    // Do Stuff
    for (
      let i = 0, len = addPriceButtons.length | 0;
      i < len;
      i = (i + 1) | 0
    ) {
      addPriceButtons[i].disabled = true
    }
  }
  const updateTotalPrice = () => {
    // Definitions
    let totalPrice = 0
    const cartTotalPrice = document.getElementById('cartTotalPrice')

    // Do Stuff
    for (
      let i = 0, len = addPriceButtons.length | 0;
      i < len;
      i = (i + 1) | 0
    ) {
      let price = addPriceButtons[i].dataset.productPrice
      console.info('ProductPrice', price)
      price = price.replace(/[^\d.-]/g, '')
      console.info('replaced productPrice')
      totalPrice += parseFloat(price) * 100 // Because I was getting rounding
      // errors at under $100
      console.info('running totalPrice:', totalPrice)
    }
    cartTotalPrice.innerText = (totalPrice / 100).toFixed(2)
  }
  const productCostPopup = (dataset) => {
    console.info(dataset)
    // Definitions
    activeProduct = dataset
    const {
      productId,
      productModel,
      productPrice,
      productManufacturer,
    } = activeProduct
    const originalPrice = document.getElementById('originalPrice')
    const priceToShow = parseFloat(productPrice).toFixed(2)
    const productModalHeader = document.getElementById('productModalHeader')
    const productPriceInput = document.getElementById('productPriceInput')

    // Do Stuff
    originalPrice.innerText = priceToShow
    productModalHeader.innerText = `${productId} ${productManufacturer} ${productModel}`
    $costModal.modal('show')
    productPriceInput.value = priceToShow
  }
  const invoiceButtonClick = () => {
    // Do Stuff
    invoiceButton.disabled = true
    destroyCartButton.disabled = true
    changeInvoiceStatus(window.DTO.CART_INVOICED)
    disablePriceButtons()
  }
  const costModalShown = () => {
    // Definitions
    const costSubmitButton = document.getElementById('costSubmitButton')

    // Actions
    const costSubmitButtonClick = () => {
      // Definitions
      const productId = activeProduct.productId
      const newPrice = document.getElementById('productPriceInput').value
      const patchData = { price: newPrice }

      // Actions
      const onPatch = (response) => {
        console.info(response)
        // Definitions
        activeProduct.productPrice = response.data.price
        const $toast = $('#productPriceToast')
        const priceText = document.getElementById(
          `productPriceButtonText_${productId}`
        )

        // Do Stuff
        $costModal.modal('hide')

        document.getElementById('toastBody').innerHTML =
          `Product ${activeProduct.productId} has been updated. ` +
          `The price is now $${activeProduct.productPrice}.`
        priceText.innerText = parseFloat(activeProduct.productPrice).toFixed(2)

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
        toastErrorBody.innerHTML = `There was an error updating the price of product ${activeProduct.productId}.<br>${error}`
        $toast.toast()
        $toast.toast('show')
      }

      // Do stuff
      if (newPrice < 0) {
        return false
      }
      axios
        .patch(`/products/${activeProduct.productId}`, patchData)
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
  for (let i = 0, len = addPriceButtons.length | 0; i < len; i = (i + 1) | 0) {
    addPriceButtons[i].addEventListener('click', () => {
      productCostPopup(addPriceButtons[i].dataset)
    })
  }

  invoiceButton.addEventListener('click', function () {
    invoiceButtonClick()
  })

  // Do Stuff
  updateTotalPrice()
}
