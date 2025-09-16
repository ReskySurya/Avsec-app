<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <meta name="description" content="Masuk ke akun Anda untuk mengakses dashboard">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .input-focus:focus {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .password-toggle {
            cursor: pointer;
            transition: color 0.3s ease;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4">
    <div class="relative w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8 ">
            <div class="text-center">
                <div class="mx-auto w-24 h-24 flex items-center justify-center mb-4">
                    <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class=" mb-2 sm:mb-0">
                </div>
                <span class="text-lg font-bold text-gray-800">Airport Security Reporting System</span><br>
                <span class="text-lg font-bold text-gray-800">(ASRS)</span>
            </div>

            <div class="text-center mt-3">
                <p class="text-gray-500">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mt-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <div class="text-red-700">
                             @if ($errors->has('identifier'))
                                <p class="text-sm">{{ $errors->first('identifier') }}</p>
                             @else
                                <p class="text-sm">Terjadi kesalahan. Silakan coba lagi.</p>
                             @endif
                        </div>
                    </div>
                </div>
            @endif


            <form method="POST" action="{{ url('/login') }}" class="space-y-6 pt-6" novalidate>
                @csrf

                <div class="space-y-2">
                    <label for="identifier" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-user mr-2 text-gray-400"></i>Email atau NIP
                    </label>
                    <div class="relative">
                        <input
                            type="text" name="identifier" id="identifier" value="{{ old('identifier') }}" required
                            autofocus
                            autocomplete="username" placeholder="Masukkan Email atau NIP Anda" class="input-focus w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300"
                            aria-describedby="identifier-error">
                        <i class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i> </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password Anda"
                            class="input-focus w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300"
                            aria-describedby="password-error">
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <i class="fas fa-eye password-toggle absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn-hover w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // PERUBAHAN: Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            // Mengambil nilai dari input 'identifier'
            const identifier = document.getElementById('identifier').value;
            const password = document.getElementById('password').value;

            if (!identifier || !password) {
                e.preventDefault();
                alert('Mohon lengkapi Email/NIP dan Password');
            }
        });

        // PERUBAHAN: Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            // Memberi fokus pada input 'identifier'
            document.getElementById('identifier').focus();
        });
    </script>
</body>
</html>
