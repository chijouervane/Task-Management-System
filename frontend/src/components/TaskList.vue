<template>
  <div class="container mt-4">
    <!-- Header Section with Create Task Button and Status Filter -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <button class="btn btn-primary" @click="openTaskForm">
        Create Task
      </button>
      <select v-model="statusFilter" @change="filterList" class="form-select w-auto">
        <option value="">All Tasks</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
      </select>
    </div>

    <!-- Loading Indicator: Displays when tasks are being loaded -->
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <!-- Task Table: Displays the list of tasks when not loading -->
    <div v-else class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-light">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td>
            <a href="#" @click.prevent="openTaskDetail(task)">{{ task.title }}</a>
          </td>
          <td>
            {{ task.description }}
          </td>
          <td>
            <!-- Conditional badge based on task status -->
            <span :class="task.status === 'completed' ? 'badge text-bg-success' : 'badge text-bg-secondary'">
              {{ task.status }}
            </span>
          </td>
          <td class="d-flex justify-content-center">
            <!-- Edit Task Button -->
            <button class="btn btn-sm btn-outline-primary me-2" @click="openTaskForm(task)">
              <i class="bi bi-pencil-square"></i>
            </button>
            <!-- Delete Task Button with loading indicator while deleting -->
            <button class="btn btn-sm btn-outline-danger" @click="deleteTask(task.id)" :disabled="deletingTaskId === task.id">
              <span v-if="deletingTaskId === task.id && !isTaskDetailVisible" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <i v-else class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls: For navigating through task pages -->
    <nav aria-label="Task pagination">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)" :disabled="currentPage === 1">
            Previous
          </button>
        </li>
        <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: currentPage === page }">
          <button class="page-link" @click="changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">
            Next
          </button>
        </li>
      </ul>
    </nav>

    <!-- Task Form Modal for Creating or Editing Tasks -->
    <TaskForm v-if="isTaskFormVisible" @close="isTaskFormVisible = false" :task="taskToEdit" :isSubmitting="isSubmitting" :submit="submitTaskForm" />
    <!-- Task Item Modal for Viewing Task Details -->
    <TasKItem v-if="isTaskDetailVisible" @close="isTaskDetailVisible = false" :task="selectedTask" :isDeleting="isDeleting" :deleteTask="deleteTask" :submit="submitTaskForm" />
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import axiosClient from "../axios.js";
import TaskForm from './TaskForm.vue';
import TasKItem from "./TasKItem.vue";

// Reactive properties for tasks, loading state, pagination, and other task details
const tasks = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const statusFilter = ref('');
const taskToEdit = ref(null);
const isTaskFormVisible = ref(false);
const isTaskDetailVisible = ref(false);
const deletingTaskId = ref(null);
const isSubmitting = ref(false);
const selectedTask = ref(null);
const isDeleting = ref(false);

// Function to fetch tasks from the backend API
const fetchTasks = async () => {
  loading.value = true;
  try {
    const response = await axiosClient.get('/tasks', {params: {page: currentPage.value, status: statusFilter.value}});
    tasks.value = response.data.data;
    totalPages.value = response.data.meta.last_page;
  } catch (error) {
    console.error('Error fetching tasks:', error);
  }
  loading.value = false;
};

// Function to open the task form for creating a new task or editing an existing one
const openTaskForm = (task = null) => {
  taskToEdit.value = task ? {...task} : {title: '', description: '', status: 'pending'};
  isTaskFormVisible.value = true;
};

// Function to submit the task form (either creating or updating a task)
const submitTaskForm = async (task) => {
  isSubmitting.value = true;
  try {
    if (task.id) {
      // Update existing task
      await axiosClient.put(`/tasks/${task.id}`, task);

      // Update the task locally in the tasks array
      const taskIndex = tasks.value.findIndex(t => t.id === task.id);
      if (taskIndex !== -1) {
        tasks.value[taskIndex] = task; // Update the task
      }

      isSubmitting.value = false;
      isTaskFormVisible.value = false;

    } else {
      // Create new task
      await axiosClient.post('/tasks', task);
      isSubmitting.value = false;
      isTaskFormVisible.value = false;
      await fetchTasks(); // Fetch updated tasks after creation
    }
  } catch (error) {
    console.error('Error submitting task:', error);
    isSubmitting.value = false;
  }
};

// Function to delete a task
const deleteTask = async (taskId) => {
  deletingTaskId.value = taskId;
  isDeleting.value = true;
  try {
    await axiosClient.delete(`/tasks/${taskId}`);
    isTaskDetailVisible.value = false;
    // Remove the task from the local tasks array
    tasks.value = tasks.value.filter(task => task.id !== taskId);

    const remainingTasks = tasks.value.length - 1; // Remaining tasks on the current page
    if (remainingTasks === 0 && currentPage.value > 1) {
      // If no tasks left on the current page and not on the first page
      currentPage.value -= 1; // Go to the previous page
    }
  } catch (error) {
    console.error('Error deleting task:', error);
  }
  deletingTaskId.value = null;
  isDeleting.value = false;
};

// Function to change the page in the pagination
const changePage = (page) => {
  currentPage.value = page;
  fetchTasks();
};

// Function to apply the status filter to the task list
const filterList = () => {
  currentPage.value = 1; // Reset to first page when filtering
  fetchTasks();
};

// Function to open the task detail modal
const openTaskDetail = (task) => {
  selectedTask.value = task;
  isTaskDetailVisible.value = true;
};

// Fetch tasks when the component is mounted
onMounted(fetchTasks);
</script>

<style>

</style>
