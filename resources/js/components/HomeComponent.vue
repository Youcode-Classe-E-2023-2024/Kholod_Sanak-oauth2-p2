<template>
    <div class="home-component">
        <h1>Welcome to the Home Page</h1>
        <p>This is the home page content.</p>
        <div v-if="userRole === 'admin'">
            <h1>You are logged in as an Admin.</h1> <br>

            <h2>User Management</h2>
            <table id="user-table" class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                        <button class="btn btn-primary" @click="openEditModal(user)">Edit</button>
                        <button class="btn btn-danger" @click="deleteUser(user.id)">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h1>You are logged in as a regular user.</h1>
        </div>

        <!-- Edit User Modal -->
        <div class="modal" id="editUserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEditForm">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editName" v-model="editUser.name">
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" v-model="editUser.email">
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="editPassword" v-model="editUser.password">
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
            users: [], // User data fetched from the API
            editUser: { // Data for editing user
                id: null,
                name: '',
                email: '',
                password: ''
            }
        };
    },
    mounted() {

        // Retrieve user role from local storage
        this.userRole = localStorage.getItem('userRole');

        // Fetch users from the API
        this.fetchUsers();
    },

    methods: {
        // Function to fetch users from the API and populate the table
        fetchUsers() {
            // Get the token from local storage
            var token = localStorage.getItem('token');

            // Make a GET request to your API endpoint to fetch users
            axios.get('/api/users', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    // Update the users data in the component with the response data
                    this.users = response.data;
                })
                .catch(error => {
                    // Handle error
                    console.error('Error fetching users:', error);
                });
        },
        openEditModal(user) {
            // Populate the editUser object with user data
            this.editUser.id = user.id;
            this.editUser.name = user.name;
            this.editUser.email = user.email;
            this.editUser.password = '';

            // Open the edit user modal
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        },
        submitEditForm() {
            // Prepare the data to be sent in the PUT request
            const userData = {
                id: this.editUser.id,
                name: this.editUser.name,
                email: this.editUser.email,
                password: this.editUser.password,

            };

            // Get the token from local storage
            const token = localStorage.getItem('token');

            // Make a PUT request to the API endpoint to update the user's role
            axios.put(`/api/users/${this.editUser.id}`, userData, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('User User updated successfully:', response.data);

                    // Close the edit user modal if needed
                    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    editModal.hide();

                })
                .catch(error => {
                    // Handle error
                    console.error('Error updating user infos:', error);
                });
        },

        deleteUser(userId) {
            const token = localStorage.getItem('token');

            // Make a DELETE request to the API endpoint to delete the user
            axios.delete(`/api/users/${userId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('User deleted successfully:', response.data);
                    // Reload users after deletion
                    this.fetchUsers();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error deleting user:', error);
                });
        }

    }

};
</script>

<style scoped>
/* Your component-specific styles */
</style>
