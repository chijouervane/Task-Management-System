<!-- TaskForm.vue -->
<template>
  <div class="modal fade show modal-overlay d-block" style="background: rgba(0, 0, 0, 0.5);" @click.self="$emit('close')">
    <div class="modal-dialog" v-if="task">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ task.id ? 'Edit Task' : 'Create Task' }}</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body text-start">
          <form @submit.prevent="submit(task)">
            <div class="mb-3">
              <label for="title" class="form-label">Title</label>
              <input
                  type="text"
                  class="form-control"
                  id="title"
                  v-model="task.title"
                  required
              />
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea
                  class="form-control"
                  id="description"
                  v-model="task.description"
                  rows="3"
                  required
              ></textarea>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select v-model="task.status" class="form-select">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
              <button type="submit" class="btn btn-primary ms-2">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ task.id ? 'Update' : 'Create ' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {defineProps} from 'vue';

defineProps({
  task: {
    type: Object,
    default: () => ({ title: '', description: '', status: 'pending' })
  },
  isVisible: {
    type: Boolean,
    default: false
  },
  isSubmitting: {
    type: Boolean,
    required: true,
  },
  submit: Function,
});

</script>

<style scoped>
.modal {
  display: block;
}

.modal-overlay {
  background: rgba(0, 0, 0, 0.7);
}
</style>
