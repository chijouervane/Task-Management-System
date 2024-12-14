<template>
  <div
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.5);"
      @click.self="$emit('close')"
  >
    <div class="modal-dialog modal-dialog-centered" v-if="task">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Task detail</h5>
        </div>
        <div class="modal-body text-start">
          <div>
            <label class="form-label">Title</label>
            <p>{{ task.title }}</p>
          </div>
          <div>
            <label class="form-label">Description</label>
            <p>{{ task.description }}</p>
          </div>
          <div>
            <label class="form-label">Status</label>
            <p>
              <span :class="task.status === 'completed' ? 'badge text-bg-success' : 'badge text-bg-secondary'">
                {{ task.status }}
              </span>
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <button
              type="button"
              :class="task.status === 'completed' ? 'btn btn-outline-secondary' : 'btn btn-outline-success'"
              @click="toggleStatus()"
          >
            <span v-if="statusUpdating" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ task.status === 'completed' ? 'Mark as Pending' : 'Mark as Completed' }}
          </button>
          <button type="button" class="btn btn-danger" @click="deleteTask(task.id)">
            <span v-if="isDeleting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {defineProps, ref} from 'vue';

// Define props
const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
  isDeleting: {
    type: Boolean,
    required: true,
  },
  deleteTask: Function,
  submit: Function,
});

const statusUpdating = ref(false);
// Function to toggle task status (mark as completed/pending)
const toggleStatus = async () => {
  statusUpdating.value = true;
  await props.submit(props.task);
  props.task.status = props.task.status === 'completed' ? 'pending' : 'completed';
  statusUpdating.value = false;
}
</script>

<style>
.modal-body label {
  font-weight: bold;
  display: block;
  margin-bottom: 0.2rem;
}
</style>
