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

                </tbody>
            </table>
        </div>
        <div v-else>
            <h1>You are logged in as a regular user.</h1>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            userRole: null
        };
    },
    mounted() {
        // Retrieve user role from local storage
        this.userRole = localStorage.getItem('userRole');

        //Display users
        // Get the token from local storage
        var token = localStorage.getItem('token');

        // Function to fetch users from API and populate the table
        function fetchUsers() {
            axios.get('/api/users', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
                .then(function(response) {
                    // Clear existing table rows
                    var tableBody = document.querySelector('#user-table tbody');
                    tableBody.innerHTML = '';

                    // Loop through each user and append a table row
                    response.data.forEach(function(user) {
                        var row = '<tr>';
                        row += '<td>' + user.id + '</td>';
                        row += '<td>' + user.name + '</td>';
                        row += '<td>' + user.email + '</td>';
                        row += '<td><button class="btn btn-primary">Edit</button> <button class="btn btn-danger">Delete</button></td>';
                        row += '</tr>';
                        tableBody.innerHTML += row;
                    });
                })
                .catch(function(error) {
                    console.error(error);
                });
        }

        // Initial fetch of users when the component is mounted
        fetchUsers();
    }
    
};
</script>

<style scoped>
/* Your component-specific styles */
</style>
