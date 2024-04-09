@extends('layouts.app')

@section('content')

    <home-component>

    </home-component>





@endsection

@section('scripts')

    {{-- users table--}}
    {{-- show users--}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Initial fetch of users when the page loads
            fetchUsers();
        });
    </script>
@endsection
