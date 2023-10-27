<template>
  <div>
    <table class="table table-hover table-bordered">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Start Date</th>
          <th scope="col">End Date</th>
          <th scope="col">Value</th>
          <th scope="col">Status</th>
          <th scope="col">Creator</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody v-if="projects && projects.length > 0">
        <tr v-for="(project, index) in projects" :key="index">
          <th scope="row">{{ project.id }}</th>
          <td>{{ project.name }}</td>
          <td>{{ project.start_date }}</td>
          <td>{{ project.end_date }}</td>
          <td>{{ project.value }}</td>
          <td class="align-middle text-center">
            <span class="status-circle d-inline-block"
              :class="{ 'active': project.status == 'active', 'inactive': project.status == 'inactive' }">
            </span>
          </td>
          <td>{{ project.creator }}</td>
          <td>
            <div class="btn-group" role="group" aria-label="Basic example">
              <button type="button" class="btn btn-outline-primary" @click="$emit('edit', index)">Edit</button>
              <button type="button" class="btn btn-outline-danger" @click="$emit('delete', project.id)">Delete</button>
            </div>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="8" class="text-center">No results Found...</td>
        </tr>

      </tbody>
    </table>
  </div>
</template>

<script setup>
const props = defineProps({
  projects: Array,
});

const emit = defineEmits(['delete', 'edit'])

</script>

<style scoped>
.status-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

.active {
  background-color: rgb(80, 199, 80);
}

.inactive {
  background-color: red;
}
</style>