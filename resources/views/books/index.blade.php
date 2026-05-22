@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{ asset('public/css/books/index.css') }}">

@endsection

@section('content')

<body>
    <section class="faq-section">
        <div class="faq-container">
            <div class="faq-header">
                <h2>Frequently Asked Questions</h2>
                <button class="read-more-btn">Read more</button>
            </div>

            <h3 class="faq-subtitle">Getting Started</h3>

            <div class="faq-list">

                <!-- Collapsible FAQ Item -->
                <div class="faq-item">
                    <p>
                        <a class="faq-toggle" data-bs-toggle="collapse" href="#registerCollapse" role="button"
                            aria-expanded="false" aria-controls="registerCollapse">
                            <strong>How can I register?</strong>
                        </a>
                    </p>
                    <div class="collapse" id="registerCollapse">
                        <div class="faq-video mt-2">
                            <video width="100%" controls>
                                <source src="{{ asset('videos/how_to_register.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>


                <div class="faq-item">
                    <p><strong>Who can register?</strong></p>
                </div>

            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ✅ JavaScript Toggle Functions -->
    <script>
        const toggleBtn = document.getElementById('customMenuToggle');
        const closeBtn = document.getElementById('customMenuClose');
        const routeWrapper = document.getElementById('routeWrapper');

        toggleBtn.addEventListener('click', () => {
            routeWrapper.classList.add('open');
        });

        closeBtn.addEventListener('click', () => {
            routeWrapper.classList.remove('open');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                routeWrapper.classList.remove('open');
            }
        });
    </script>
    @endsection
    @section('footer')

    <footer>
        <div class="a51-footer">
            <h4 style="color: white; font-size:15px">Pantas © 2025. All Rights Reserved.</h4>
        </div>
    </footer>
    @endsection