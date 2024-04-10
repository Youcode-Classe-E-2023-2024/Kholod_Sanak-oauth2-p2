<template>
    <div class="role-component">

        <div v-if="userRole === 'admin'">
            <br> <br>

            <h2>Role Management</h2>
            <button class="btn btn-success" @click="openAddModal()">Add</button>

            <table id="role-table" class="table">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="role in roles" :key="role.id">
                    <td>{{ role.id }}</td>
                    <td>{{ role.name }}</td>
                    <td>
                        <button class="btn btn-primary" @click="openEditModal(role)">Edit</button>
                        <button class="btn btn-danger" @click="deleteRole(role.id)">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
        </div>

        <!-- Add Role Modal -->
        <div class="modal" id="addRoleModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitAddForm">
                            <div class="mb-3">
                                <label for="addRoleName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="addRoleName" v-model="addRole.name">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Role Modal -->
        <div class="modal" id="editRoleModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEditForm">
                            <div class="mb-3">
                                <label for="editRoleName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editRoleName" v-model="editRole.name">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            userRole: null,
            roles: [],
            addRole: {
                name: ''
            },
            editRole: {
                id: null,
                name: ''
            }
        };
    },
    mounted() {
        // Retrieve user role from local storage
        this.userRole = localStorage.getItem('userRole');

        // Fetch roles from the API
        this.fetchRoles();
    },

    methods: {
        fetchRoles() {
            // Get the token from local storage
            var token = localStorage.getItem('token');

            // Make a GET request to your API endpoint to fetch roles
            axios.get('/api/roles', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {

                    this.roles = response.data;
                })
                .catch(error => {
                    // Handle error
                    console.error('Error fetching roles:', error);
                });
        },
        openAddModal() {
            // Clear add role data
            this.addRole = {
                name: ''
            };

            // Open the add role modal
            const addModal = new bootstrap.Modal(document.getElementById('addRoleModal'));
            addModal.show();
        },
        submitAddForm() {
            // Prepare the data to be sent in the POST request
            const roleData = {
                name: this.addRole.name
            };

            // Get the token from local storage
            const token = localStorage.getItem('token');

            // Make a POST request to the API endpoint to add a new role
            axios.post('/api/roles', roleData, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('Role added successfully:', response.data);

                    // Close the add role modal
                    const addModal = new bootstrap.Modal(document.getElementById('addRoleModal'));
                    addModal.hide();

                    // Reload roles after adding a new role
                    this.fetchRoles();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error adding role:', error);
                });
        },

        submitEditForm() {
            // Prepare the data to be sent in the PUT request
            const roleData = {
                id: this.editRole.id,
                name: this.editRole.name
            };

            // Get the token from local storage
            const token = localStorage.getItem('token');

            // Make a PUT request to the API endpoint to update the role
            axios.put(`/api/roles/${this.editRole.id}`, roleData, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('Role updated successfully:', response.data);

                    // Close the edit role modal if needed
                    const editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
                    editModal.hide();

                    // Reload roles after updating a role
                    this.fetchRoles();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error updating role:', error);
                });
        },

        openEditModal(role) {
            // Populate the editRole object with role data
            this.editRole.id = role.id;
            this.editRole.name = role.name;

            // Open the edit role modal
            const editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            editModal.show();
        },


        deleteRole(roleId) {
            const token = localStorage.getItem('token');

            // Make a DELETE request to the API endpoint to delete the role
            axios.delete(`/api/roles/${roleId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('Role deleted successfully:', response.data);
                    // Reload roles after deletion
                    this.fetchRoles();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error deleting role:', error);
                });
        }

    }

};
</script>

<style scoped>
/* Your component-specific styles */
</style>
