<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="modal fade" id="modal_create" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" data-bs-backdrop="static">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            {{ form.id ? 'Edit Project' : 'Create Project' }}
                        </h5>
                        <button type="button" class="btn btn-outline-danger" @click="resetForm" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input v-model="form.name" type="email" class="form-control" id="name"
                                    placeholder="Project Name">
                                <div v-if="responseErrors.name" class="text-danger">
                                    {{ responseErrors.name[0] }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="start-date" class="form-label">Start Date</label>
                                <input v-model="form.start_date" type="datetime-local" class="form-control" id="start-date">
                                <div v-if="responseErrors.start_date" class="text-danger">
                                    {{ responseErrors.start_date[0] }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="end-date" class="form-label">End Date</label>
                                <input v-model="form.end_date" type="datetime-local" class="form-control" id="end-date">
                                <div v-if="responseErrors.end_date" class="text-danger">
                                    {{ responseErrors.end_date[0] }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="value" class="form-label">Value</label>
                                <input v-model="form.value" type="number" class="form-control" id="value"
                                    placeholder="99,99">
                                <div v-if="responseErrors.value" class="text-danger">
                                    {{ responseErrors.value[0] }}
                                </div>
                            </div>
                            <div class="mb-3" v-if="form.creator">
                                <label for="creator" class="form-label">Creator</label>
                                <input v-model="form.creator" type="text" class="form-control" id="creator" disabled>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="status" @change="updateStatus"
                                    :checked="form.status == 'active'">
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click="submit" type="button" class="btn btn-outline-primary">{{ form.id ? 'Save' :
                            'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>

        <template #header>
            <h2 class="font-semibold text-xl text-dark">Dashboard</h2>
        </template>
        <div v-if="alerts" class="alert w-50 mx-auto mt-6" role="alert" :class="alerts.type">
            {{ alerts.message }}
        </div>
        <div class="container">
            <div class="overflow-hidden shadow-sm rounded card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <p>Projects</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_create">
                        New Project
                    </button>
                </div>
                <div class="alert alert-danger" role="alert" v-if="responseErrors.error">
                    {{ responseErrors.error }}
                </div>
                <TableComponent :projects="data.data" @delete="deleteProject" @edit="getEditingProject" />
            </div>
            <div class="pagination">
                <Bootstrap5Pagination :data="data" @pagination-change-page="getTableData" />
            </div>
        </div>

    </AuthenticatedLayout>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TableComponent from '@/Components/TableComponent.vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';


export default {
    components: {
        AuthenticatedLayout,
        TableComponent,
        Head,
        Bootstrap5Pagination
    },
    data() {
        return {
            alerts: {},
            data: {},
            form: {
                status: 'active',
            },
            responseErrors: {},
        };
    },
    async created() {
        this.getTableData();
    },
    methods: {
        async getTableData(page = 1) {
            await axios.get(`/getProjects?page=${page}`)
                .then(response => {
                    this.data = response.data
                })
                .catch(errors => {
                    this.responseErrors = errors.response.data;
                });
        },
        submit() {
            const route = this.form.id ? '/updateProject' : '/storeProject';

            axios.post(route, this.form)
                .then(response => {

                    this.getTableData();
                    this.resetForm();
                    $('#modal_create').modal('hide');

                    this.alerts = {
                        message: response.data.success,
                        type: 'alert-success'
                    }
                })
                .catch(errors => {
                    if (errors.response.status == 404) {
                        this.responseErrors = errors.response.data;
                    } else {
                        this.responseErrors = errors.response.data.errors;
                    }
                });
        },
        getEditingProject(index) {
            this.form = Object.assign({}, this.data.data[index]);
            $('#modal_create').modal('show');
        },
        deleteProject(project_id) {
            if (confirm('Are you sure you want to remove it?') == false) {
                return;
            }
            axios.delete(`/deleteProject/${project_id}`)
                .then(response => {
                    this.getTableData();

                    this.alerts = {
                        message: response.data.success,
                        type: 'alert-success'
                    }
                })
                .catch(errors => {
                    this.responseErrors = errors.response.data;
                });
        },
        resetForm() {
            this.form = {
                status: 'active',
            };
            this.responseErrors = {}
        },
        updateStatus(event) {
            this.form.status = event.target.checked ? 'active' : 'inactive';
        },
    },
    watch: {
        alerts(newAlerts) {
            if (newAlerts) {
                setTimeout(() => {
                    this.alerts = {};
                }, 5000);
            }
        },
    },
};
</script>

<style scoped></style>
