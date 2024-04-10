<template>
    <div class="user-component">

        <div v-if="userRole === 'admin'">
            <h1>You are logged in as an Admin.</h1> <br>

            <h2>User Management</h2>
            <button class="btn btn-success" @click="openAddModal()">Add</button>

            <table id="user-table" class="table">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th> Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
<!--                    <td>-->
<!--                        <select v-model="user.role_id" class="form-select">-->
<!--                            <option v-if="!user.role" disabled selected>Select Role</option>-->
<!--                            <option v-for="role in roles" :value="role.id" :key="role.id">{{ role.name }}</option>-->
<!--                        </select>-->
<!--                    </td>                    <td>-->
                    <td>{{user.role.name}}</td>
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

        <!-- Add User Modal -->
        <div class="modal" id="addUserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitAddForm">
                            <div class="mb-3">
                                <label for="addName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="addName" v-model="addUser.name">
                            </div>
                            <div class="mb-3">
                                <label for="addEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="addEmail" v-model="addUser.email">
                            </div>
                            <div class="mb-3">
                                <label for="addPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="addPassword" v-model="addUser.password">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
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
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-select" id="editRole" v-model="editUser.role_name">
                                    <option value="" disabled>Select Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                                </select>
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
            users: [],
            roles: [],
            addUser:{
                name:'',
                email:'',
                password:''
            },
            editUser: {
                id: null,
                name: '',
                email: '',
                password: '',
                role_name:''
            }
        };
    },
    mounted() {

        this.userRole = localStorage.getItem('userRole');

        this.fetchUsers();
        this.fetchRoles();

    },

    methods: {
        // Function to fetch users from the API and populate the table
        fetchUsers() {
            // Get the token from local storage
            var token = localStorage.getItem('token');

            axios.get('/api/users', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {

                    this.users = response.data;
                })
                .catch(error => {
                    // Handle error
                    console.error('Error fetching users:', error);
                });
        },
        fetchRoles() {
            // Get the token from local storage
            var token = localStorage.getItem('token');

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
            // Clear add user data
            this.addUser = {
                name: '',
                email: '',
                password: ''
            };

            // Open the add user modal
            const addModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            addModal.show();
        },
        submitAddForm() {
            // Prepare the data to be sent in the POST request
            const userData = {
                name: this.addUser.name,
                email: this.addUser.email,
                password: this.addUser.password
            };

            // Get the token from local storage
            const token = localStorage.getItem('token');

            // Make a POST request to the API endpoint to add a new user
            axios.post('/api/users', userData, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(response => {
                    // Handle successful response
                    console.log('User added successfully:', response.data);

                    // Close the add user modal
                    const addModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                    addModal.hide();

                    // Reload users after adding a new user
                    this.fetchUsers();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error adding user:', error);
                });
        },

        openEditModal(user) {
            // Populate the editUser object with user data
            this.editUser.id = user.id;
            this.editUser.name = user.name;
            this.editUser.email = user.email;
            this.editUser.password = '';
            // this.editUser.role_name=user.role;

            // Open the edit user modal
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        },

        submitEditForm() {
            const userData = {
                id: this.editUser.id,
                name: this.editUser.name,
                email: this.editUser.email,
                password: this.editUser.password,
                role_name: this.editUser.role_name
            };


                const token = localStorage.getItem('token');

                const config = {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                };

                // Send a PUT request to your backend API to assign the role to the user
                axios.put(`/api/users/${this.editUser.id}`, userData, config)
                    .then(response => {
                        // Handle successful response
                        console.log('User updated and Role assigned successfully:', response.data);

                        // Close the edit user modal if needed
                        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                        editModal.hide();
                    })
                    .catch(error => {
                        // Handle error
                        console.error('Error assigning role:', error);
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
