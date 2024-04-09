<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto" id="authLinks">
                        <!-- Authentication Links -->

                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{--Register--}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the form element
        const form = document.getElementById('registerForm');

        // Add event listener for form submission
        form.addEventListener('submit', function(event) {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Collect form data using FormData constructor with event.target
            const formData = new FormData(event.target);

            // Send AJAX request using Axios
            axios.post('{{ url('api/auth/register') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data', // Set content type for FormData
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => {
                    // Handle successful registration
                    console.log('Registration successful');
                    window.location.href = '{{ route('login') }}';
                })
                .catch(error => {
                    // Handle registration error
                    console.error('Registration failed:', error);
                    // Show error message to the user
                });
        });
    });
</script>
{{--Login--}}
<script>
document.addEventListener("DOMContentLoaded", function() {
        // Get the form element
        const form = document.getElementById('loginForm');

        // Add event listener for form submission
        form.addEventListener('submit', function(event) {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Collect form data using FormData constructor with event.target
            const formData = new FormData(event.target);

            // Send AJAX request using Axios
            axios.post('{{ url('api/auth/login') }}', formData)
                .then(response => {
                    // Check if the response contains the token
                    if (response.data.token) {
                        // Handle successful login
                        console.log('Login successful');
                        console.log(response);
                        // console.log( response.data.username);


                        localStorage.setItem('token', response.data.token);
                        localStorage.setItem('userName',response.data.username);
                        localStorage.setItem('userRole',response.data.role);
                        // let userRole = response.data.role;


                        // Redirect the user to the home page
                        window.location.href = '{{ route('home') }}';
                    } else {
                        console.error('Token not found in response:', response);
                    }
                })
                .catch(error => {
                    // Handle login error
                    console.error('Login failed:', error);
                });
        });
    });
</script>
{{--Navbar--}}
<script>
 document.addEventListener("DOMContentLoaded", function() {
        const authLinks = document.getElementById('authLinks');

        // Check if the user is authenticated
        const token = localStorage.getItem('token');
        if (token) {
            const userName = localStorage.getItem('userName');
            authLinks.innerHTML =  `
    <li class="nav-item ">
        <a class="nav-link" href="#" >
            ${userName}
        </a>

     <li><a class="nav-link" href="#" onclick="logout()">Logout</a></li>

    </li>
`;
        } else {

            authLinks.innerHTML = `
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>
            `;
        }
    });

    // Function to handle logout
    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('userName');
        window.location.href = '{{ route('login') }}';
    }
</script>

{{--authentifated pages--}}

</html>
