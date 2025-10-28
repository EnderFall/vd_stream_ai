<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Scheduler & Financial Report')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Video Streaming
                        </a>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="{{ route('schedules.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('schedules.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-calendar mr-1"></i>Schedules
                        </a>
                        <a href="{{ route('financial.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('financial.*') || request()->routeIs('transactions.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-money-bill-wave mr-1"></i>Financial
                        </a>
                        <a href="{{ route('targets.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('targets.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-bullseye mr-1"></i>Targets
                        </a>
                        <a href="{{ route('va.recommendations') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('va.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-robot mr-1"></i>AI Assistant
                        </a>
                        <a href="{{ route('chat.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-comments mr-1"></i>Chatbot
                        </a>
                        <a href="{{ route('notifications.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notifications.*') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-bell mr-1"></i>Notifications
                        </a>
                    </div>
                </div>
                <div class="hidden md:ml-6 md:flex md:items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-4">
                            <span class="text-white text-sm">{{ Auth::user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button" class="bg-blue-600 inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h5 class="text-lg font-semibold mb-4">Video Streaming App With Smart Scheduler & Financial Report</h5>
                    <p class="text-gray-300">AI-powered platform for influencers to stream video also manage schedules and track financial performance.</p>
                </div>
                <div>
                    <h6 class="text-lg font-semibold mb-4">Features</h6>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-video mr-2"></i>Video Streaming</li>
                        <li><i class="fas fa-calendar mr-2"></i>Smart Scheduling</li>
                        <li><i class="fas fa-money-bill-wave mr-2"></i>Financial Tracking</li>
                        <li><i class="fas fa-robot mr-2"></i>AI Recommendations</li>
                        <li><i class="fas fa-chart-line mr-2"></i>Monthly Reports</li>
                    </ul>
                </div>
                <div>
                    <h6 class="text-lg font-semibold mb-4">Contact</h6>
                    <p class="text-gray-300">
                        <i class="fas fa-envelope mr-2"></i>support@videostream.com<br>
                        <i class="fas fa-phone mr-2"></i>+628123456789
                    </p>
                </div>
            </div>
            <hr class="my-8 border-gray-600">
            <div class="text-center">
                <p class="text-gray-300">&copy; {{ date('Y') }} Video Streaming. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('scripts')
</body>
</html>
