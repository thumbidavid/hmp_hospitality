import { computed, reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useModalStore } from '@/Stores/modalStore'
import Swal from 'sweetalert2'
import ResourceModal from '@/Components/Admin/ResourceModal.vue' // The shared modal layout

export function useResourceCRUD(config) {
  const {
    resourceName,
    entityName = resourceName.toLowerCase(),
    routeNames,
    routeParams = () => ({}), // ✅ optional: supports nested resources like events/{event}/tasks
    initialFormState = () => ({}),
    formTransform = (form) => form,
    onSuccess,
    onError,
  } = config

  const modalStore = useModalStore()

  const currentItem = reactive({ ...initialFormState() })
  const isEditing = ref(false)
  const isLoading = ref(false)
  const formErrors = ref({})

  // -------------------------
  // Helper: reset form to initial state
  // -------------------------
  const resetForm = () => {
    Object.assign(currentItem, initialFormState())
    formErrors.value = {}
  }

  // -------------------------
  // Open Create Modal
  // -------------------------
  const openCreateModal = (initialData = {}) => {
    resetForm()
    Object.assign(currentItem, initialData) // Pre-fill fields if needed
    isEditing.value = false

    console.log(`Opening Create Modal for ${resourceName} with initial data:`, initialData)

    modalStore.open({
      component: ResourceModal,
      props: {
        title: `Create New ${resourceName}`,
        description: config.description,
        submitButtonText: `Create ${resourceName}`,
        isLoading: isLoading.value,
        mode: 'create',
        onSubmit: submitForm,
        onClose: closeModal,
        currentItem,
        formErrors,
        fieldsConfig: config.fieldsConfig,
      },
    })
  }

  // -------------------------
  // Open Edit Modal
  // -------------------------
  const openEditModal = (itemToEdit) => {
    resetForm()
    const itemCopy = JSON.parse(JSON.stringify(itemToEdit)) // Deep clone
    Object.assign(currentItem, itemCopy)
    isEditing.value = true

    modalStore.open({
      component: ResourceModal,
      props: {
        title: `Edit ${resourceName}`,
        description: config.description,
        submitButtonText: 'Save Changes',
        isLoading: isLoading.value,
        mode: 'edit',
        onSubmit: submitForm,
        onClose: closeModal,
        currentItem,
        formErrors,
        fieldsConfig: config.fieldsConfig,
      },
    })
  }

  // -------------------------
  // Close Modal
  // -------------------------
  const closeModal = () => {
    modalStore.close()
  }

  // -------------------------
  // Submit Form (Create or Update)
  // -------------------------
  const submitForm = () => {
    isLoading.value = true
    formErrors.value = {}

    const dataToSubmit = formTransform(currentItem, isEditing.value)

    // 💡 DEBUG: Determine the route and parameters for logging
    const routeName = isEditing.value ? routeNames.update : routeNames.store
    const routeData = isEditing.value
      ? { [entityName]: currentItem.id, ...routeParams() }
      : routeParams()

    // 💡 DEBUG: Log the data and route being called
    console.log(
      `--- DEBUG: Submitting ${isEditing.value ? 'UPDATE' : 'CREATE'} for ${resourceName} ---`
    )
    console.log('Route Name:', routeName)
    console.log('Route Parameters:', routeData)
    // NOTE: For FormData uploads, logging dataToSubmit may just show {}
    console.log('Form Data (Payload):', dataToSubmit)
    try {
      console.log('Full URL:', route(routeName, routeData)) // Use try-catch in case Ziggy fails
    } catch (e) {
      console.error('Failed to generate URL with Ziggy:', e)
    }
    console.log('---------------------------------------------------------------------------------')

    const options = {
      preserveScroll: true,
      onSuccess: (page) => {
        closeModal()
        // 💡 DEBUG: Log success
        console.log(`✅ SUCCESS: ${resourceName} ${isEditing.value ? 'updated' : 'created'}.`)
        if (onSuccess) onSuccess(page, isEditing.value)
      },
      onError: (errors) => {
        formErrors.value = errors
        // 💡 DEBUG: Log form errors from backend
        console.error('❌ ERROR: Submission failed with errors:', errors)
        if (onError) onError(errors, isEditing.value)
      },
      onFinish: () => {
        isLoading.value = false
      },
    }

    // ---------- UPDATE ----------
    if (isEditing.value) {
      const routeData = {
        [entityName]: currentItem.id,
        ...routeParams(), // ✅ include extra params if needed (e.g., event ID)
      }

      if (dataToSubmit instanceof FormData) {
        router.post(route(routeNames.update, routeData), dataToSubmit, options)
      } else {
        router.put(route(routeNames.update, routeData), dataToSubmit, options)
      }
    }
    // ---------- CREATE ----------
    else {
      router.post(route(routeNames.store, routeParams()), dataToSubmit, options)
    }
  }

  // -------------------------
  // Delete Item (with confirmation)
  // -------------------------
  const deleteItem = (itemToDelete) => {
    Swal.fire({
      title: `Delete ${resourceName}?`,
      html: `Are you sure you want to delete <strong>${
        itemToDelete.name || 'this item'
      }</strong>? This cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
      if (result.isConfirmed) {
        const routeData = {
          [entityName]: itemToDelete.id,
          ...routeParams(), // ✅ include route params for nested routes
        }

        router.delete(route(routeNames.destroy, routeData), {
          preserveScroll: true,
          onSuccess: (page) => {
            // 💡 DEBUG: Log delete success
            console.log(`✅ SUCCESS: ${resourceName} deleted.`)
            if (onSuccess) onSuccess(page, 'delete')
          },
          onError: (errors) => {
            // 💡 DEBUG: Log delete error
            console.error('❌ ERROR: Deletion failed with errors:', errors)
            if (onError) onError(errors, 'delete')
          },
        })
      }
    })
  }

  // -------------------------
  // Return everything needed by the page
  // -------------------------
  return {
    currentItem,
    isEditing,
    isLoading,
    formErrors,
    openCreateModal,
    openEditModal,
    closeModal,
    submitForm,
    deleteItem,
  }
}
