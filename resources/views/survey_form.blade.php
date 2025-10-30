<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Satisfaction Survey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @laravelPWA
</head>
<body>

<div class="container">
    <div class="center-logo-container">
        <image src="{{ asset('storage/logo/logo-with-seals.png') }}">
    </div>
    <h5>HUMAN RESOURCE DEVELOPMENT OFFICE</h5>
    <h1>Customer Satisfaction Survey</h1>

    <form action="{{ route('survey.submit') }}" method="POST">
        @csrf

        <div id="surveyCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
            <div class="carousel-inner">

                {{-- SLIDE 1: Division Selection --}}
                <div class="carousel-item active">
                    <div class="question-slide">
                        <h3 style="padding-bottom: 10px">Select Division / Pumili ng Dibisyon:</h3>
                        <div class="filter-buttons division-buttons-grid">
                            <button type="button" class="btn btn-all mb-3 division-btn active" data-office-id="all">ALL DIVISIONS</button>
                            <button type="button" class="btn btn-it mb-3 division-btn" data-office-id="2">INFORMATION TECHNOLOGY</button>
                            <button type="button" class="btn btn-admin mb-3 division-btn" data-office-id="3">ADMINISTRATIVE</button>
                            <button type="button" class="btn btn-payroll mb-3 division-btn" data-office-id="4">PAYROLL</button>
                            <button type="button" class="btn btn-records mb-3 division-btn" data-office-id="5">RECORDS</button>
                            <button type="button" class="btn btn-claims mb-3 division-btn" data-office-id="6">CLAIMS & BENEFITS</button>
                            <button type="button" class="btn btn-rsp mb-3 division-btn" data-office-id="7">RSP</button>
                            <button type="button" class="btn btn-ld mb-3 division-btn" data-office-id="8">LEARNING & DEVELOPMENT</button>
                            <button type="button" class="btn btn-pm mb-3 division-btn" data-office-id="9">PERFORMANCE MANAGEMENT</button>
                        </div>
                    </div>
                </div>
                {{-- END: SLIDE 1 --}}

                {{-- SLIDE 2: Staff Selection --}}
                <div class="carousel-item" id="staff-selection-slide">
                    <div class="question-slide">
                        <h3 style="padding: 50px">Attended by / Inasikaso ni:</h3>
                        <div class="staff-scroll-container">
                            <div class="staff-scroll-panel">
                                @foreach($staffs as $staff)
                                    <div class="text-center staff-item" data-office-id="{{ $staff->office_id }}">
                                        <label>
                                            <input type="radio" name="user_id" value="{{ $staff->id }}" id="staff-{{ $staff->id }}" data-office-id="{{ $staff->office_id }}" style="display:none;" required>

                                            {{-- START: Optimized Image Loading --}}
                                            <picture>
                                                @if ($staff->getAvatarWebpUrl())
                                                    <source srcset="{{ $staff->getAvatarWebpUrl() }}" type="image/webp">
                                                @endif
                                                <img src="{{ $staff->getAvatarUrl() }}"
                                                    alt="{{ $staff->name }}'s profile picture"
                                                    class="staff-avatar"
                                                    loading="lazy">
                                            </picture>
                                            {{-- END: Optimized Image Loading --}}

                                        </label>
                                        <span class="staff-name">{{ $staff->name }}</span>
                                        <span class="staff-nickname">"{{ $staff->nickname }}"</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 go-back-btn-large" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>
                {{-- END: SLIDE 2 --}}

                {{-- SLIDE 3: Service Selection --}}
                <div class="carousel-item service-slide">
                    <div class="question-slide">
                        <h3 style="padding-bottom: 20px">Service Received / Serbisyong Natanggap:</h3>
                        <div class="service-scroll-container">
                            <div id="service-grid" class="service-scroll-panel">
                                @foreach($services as $service)
                                    <div class="service-item text-center" data-office-id="{{ $service->office_id }}" data-service-id="{{ $service->id }}" style="display: none;">
                                        <label>
                                            <input type="radio" name="problem_category_id" value="{{ $service->id }}" style="display:none;" required>
                                            <div class="service-icon-box">
                                                <x-heroicon-o-document-duplicate class="w-14 h-14 text-white-500" />
                                                </div>
                                            <span class="service-name">{{ $service->category_name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 go-back-btn-large" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>
                {{-- END: SLIDE 3 --}}

                {{-- SLIDE 4: RESPONSIVENESS (Rating 1) --}}
                <div class="carousel-item">
                    <div class="question-slide">
                        <h2>RESPONSIVENESS (PAGTUGON)</h2>
                        <p>Willingness to help, assist, and provide prompt service.</p>
                        <p>(Handang tumugon at magbigay nang mabilis na serbisyo.)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            {{-- No 'true' parameter here, as it's NOT the last question --}}
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Very Dissatisfied')">
                                <input type="radio" name="responsiveness_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                <span class="emoji-rating-text">Very Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Dissatisfied')">
                                <input type="radio" name="responsiveness_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                <span class="emoji-rating-text">Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Satisfied')">
                                <input type="radio" name="responsiveness_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                <span class="emoji-rating-text">Satisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Very Satisfied')">
                                <input type="radio" name="responsiveness_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                <span class="emoji-rating-text">Very Satisfied</span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 go-back-btn-large" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>
                {{-- END: SLIDE 4 --}}

                {{-- SLIDE 5: TIMELINESS (Rating 2) --}}
                <div class="carousel-item">
                    <div class="question-slide">
                        <h2>TIMELINESS (BILIS NG PAGTUGON)</h2>
                        <p>Satisfaction with the timeliness of service/response to your needs.</p>
                        <p>(Kontento sa bilis ng serbisyo/pagtugon sa iyong pangangailangan.)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            {{-- No 'true' parameter here, as it's NOT the last question --}}
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Very Dissatisfied')">
                                <input type="radio" name="timeliness_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                <span class="emoji-rating-text">Very Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Dissatisfied')">
                                <input type="radio" name="timeliness_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                <span class="emoji-rating-text">Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Satisfied')">
                                <input type="radio" name="timeliness_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                <span class="emoji-rating-text">Satisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Very Satisfied')">
                                <input type="radio" name="timeliness_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                <span class="emoji-rating-text">Very Satisfied</span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 go-back-btn-large" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>
                {{-- END: SLIDE 5 --}}

                {{-- SLIDE 6: COMMUNICATION (Rating 3) - LAST RATING SLIDE --}}
                <div class="carousel-item">
                    <div class="question-slide">
                        <h2>COMMUNICATION (PAKIKIPAG-USAP)</h2>
                        <p>Act of keeping citizens informed in a language they can easily understand and delivered courteously.</p>
                        <p>(Paggamit ng wika na madaling maunawaan at naipahayag ng magalang.)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            {{-- ADDED 'true' parameter here for auto-submit --}}
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Very Dissatisfied', true)">
                                <input type="radio" name="communication_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                <span class="emoji-rating-text">Very Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Dissatisfied', true)">
                                <input type="radio" name="communication_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                <span class="emoji-rating-text">Dissatisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Satisfied', true)">
                                <input type="radio" name="communication_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                <span class="emoji-rating-text">Satisfied</span>
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Very Satisfied', true)">
                                <input type="radio" name="communication_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                <span class="emoji-rating-text">Very Satisfied</span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 go-back-btn-large" onclick="prevSlide()">Go Back</button>
                        {{-- REMOVED THE SUBMIT BUTTON FROM HERE --}}
                    </div>
                </div>
                {{-- END: SLIDE 6 --}}

                {{-- SLIDE 7: QR Code / Thank You Page --}}
                <div class="carousel-item" id="qr-timeout-slide">
                    <div class="question-slide">

                        {{-- ADDED SUCCESS MESSAGE LOGIC HERE --}}
                        @if(session('success'))
                            <div id="success-message" class="alert alert-success" role="alert"
                                style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 20px;">
                                {{ session('success') }}
                            </div>
                        @endif
                        {{-- END SUCCESS MESSAGE --}}

                        <h3 style="padding: 50px">Scan the QR code to fill out the comments and suggestions form</h3>

                        <div class="qr-code-container d-flex justify-content-center">
                            {!! QrCode::size(350)->backgroundColor(255, 255, 255, 0)->generate('https://forms.gle/Tvmm2WmjHGNqteUD9') !!}
                        </div>

                        <br>

                        <div class="mt-3">
                            {{-- Button to go back to the first slide (by reloading the page) --}}
                            <button type="button" class="btn btn-primary m-3 go-back-btn-large" onclick="window.location.reload()">Rate Again</button>
                            {{-- REMOVED THE SUBMIT BUTTON --}}
                        </div>
                    </div>
                </div>
                {{-- END: SLIDE 7 --}}

            </div>
        </div>
    </form>

    <!-- Indicators -->
    {{-- <div class="carousel-indicators">
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
        <button type="button" data-bs-target="#surveyCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
    </div> --}}

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#surveyCarousel" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 5"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 6"></button>
        <button type="button" data-bs-target="#surveyCarousel" aria-label="Slide 7"></button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script>
    const surveyCarouselEl = document.getElementById('surveyCarousel');
    // Ensure the carousel instance is accessible globally for nextSlide/prevSlide
    const surveyCarousel = new bootstrap.Carousel(surveyCarouselEl, {
        touch: false,
        interval: false
    });

    function nextSlide() {
        surveyCarousel.next();
    }

    function prevSlide() {
        surveyCarousel.prev();
    }

    function submitForm() {
        const form = document.querySelector('form');
        form.submit();
    }

function selectRating(selectedLabel, inputName, value, isLastQuestion = false) {
    // 1. Handle Active State
    var container = selectedLabel.closest('.d-flex');
    var labels = container.querySelectorAll('label');
    labels.forEach(function(label) {
        // Remove the 'active' class from all buttons in the group
        label.classList.remove('active');
    });

    // 2. Set the clicked button as active and check the radio input
    selectedLabel.classList.add('active');
    var input = selectedLabel.querySelector('input[name="' + inputName + '"]');
    if (input) {
        input.checked = true;
    }

    // 3. Always transition to the next slide first
    // This moves the user to the next rating question or the final QR slide.
    nextSlide();

    // 4. Submission Logic (only for the last rating question)
    if (isLastQuestion) {
        // Wait 400ms for the slide transition to complete visually, then submit the form.
        setTimeout(function() {
            submitForm();
        }, 400); // 400ms is a safe delay for a smooth transition
    }
}

    $(document).ready(function() {

        // --- HEADER TOGGLE FUNCTION ---
        function toggleHeader(slideIndex) {
            // Check if the current slide is the first one (index 0)
            if (slideIndex === 0) {
                $('.center-logo-container, h5, h1').show();
            } else {
                // Hide the header elements on all subsequent slides
                $('.center-logo-container, h5, h1').hide();
            }
        }

        // Initialize header state on load
        toggleHeader(0);

        // --- DIVISION SELECTION AND SLIDE TRANSITION (NEW Slide 1) ---
        $('.division-btn').on('click', function() {
            var selectedOfficeId = $(this).data('office-id');

            // 1. Update active button state
            $('.division-btn').removeClass('active');
            $(this).addClass('active');

            // 2. Filter the staff list (in the *next* slide)
            if (selectedOfficeId === 'all') {
                $('.staff-item').show();
            } else {
                $('.staff-item').hide();
                $('.staff-item[data-office-id="' + selectedOfficeId + '"]').show();
            }

            // Store the selected ID to initialize the filter on the staff slide's back button click
            $('#staff-selection-slide').data('current-office-id', selectedOfficeId);

            // 3. Move to the Staff Selection slide
            setTimeout(() => {
                surveyCarousel.next();
            }, 300);
        });


        // --- STAFF SELECTION AND SLIDE TRANSITION (Slide 2) ---
        $('.staff-item label').on('click', function(e) {
            e.preventDefault();

            var $input = $(this).find('input[name="user_id"]');
            var $avatar = $(this).find('.staff-avatar');

            // Clear previous selections
            $('input[name="user_id"]').prop('checked', false);
            $('.staff-avatar').removeClass('selected');

            // Set new selection
            $input.prop('checked', true);
            $avatar.addClass('selected');

            $input.trigger('change');

            // Move to the next slide (Service Selection)
            setTimeout(() => {
                surveyCarousel.next();
            }, 300);
        });


        // --- SERVICE FILTERING (Triggered by Staff Change) ---
        // This is necessary to show the right services when navigating back to this slide (Slide 3)
        $('input[name="user_id"]').on('change', function() {
            var selectedOfficeId = $(this).data('office-id').toString();

            $('.service-item').hide();

            $('.service-item[data-office-id="' + selectedOfficeId + '"]').show();

            $('input[name="problem_category_id"]').prop('checked', false);
            $('.service-item').removeClass('selected');
        });


        // --- SERVICE SELECTION AND VISUAL FEEDBACK (Service Slide 3) ---
        $('.service-item').on('click', function() {
            $('.service-item').removeClass('selected');

            $(this).addClass('selected');

            $(this).find('input[name="problem_category_id"]').prop('checked', true);

            setTimeout(() => {
                surveyCarousel.next();
            }, 300);
        });

        // --- CAROUSEL SLIDE EVENT HANDLER (Main Logic Controller) ---
        $('#surveyCarousel').on('slid.bs.carousel', function (e) {
            const currentSlideIndex = e.to;
            const $relatedTarget = $(e.relatedTarget);

            // 1. Toggle Header Visibility
            toggleHeader(currentSlideIndex);

            // 2. Service Filtering Fallback (on slide to Service Selection)
            if ($relatedTarget.hasClass('service-slide')) {
                 $('input[name="user_id"]:checked').trigger('change');
            }

            // 3. Update Indicators (This remains the same)
            const indicators = document.querySelectorAll('.carousel-indicators button');
            indicators.forEach((indicator, index) => {
                indicator.classList.remove('active');
                indicator.removeAttribute('aria-current');
                if (index === currentSlideIndex) {
                    indicator.classList.add('active');
                    indicator.setAttribute('aria-current', 'true');
                }
            });
        });

        // Auto-hide success message after 3 seconds
        setTimeout(() => {
            $('#success-message').fadeOut('slow');
        }, 5000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const surveyCarousel = document.getElementById('surveyCarousel');
        const qrSlide = document.getElementById('qr-timeout-slide');
        let timeoutId; // Variable to hold the timer ID

        if (surveyCarousel && qrSlide) {
            // 1. Listen for the Bootstrap Carousel slide event
            surveyCarousel.addEventListener('slid.bs.carousel', function () {
                // Check if the currently active slide is the QR slide
                if (qrSlide.classList.contains('active')) {
                    // Clear any existing timer just in case
                    clearTimeout(timeoutId);

                    // Set a new timer for 3 minutes (3 minutes * 60 seconds * 1000 milliseconds)
                    const timeoutDuration = 3 * 60 * 1000;
                    console.log('QR slide active. Setting timeout for 3 minutes.');

                    timeoutId = setTimeout(function() {
                        // Action to perform after 3 minutes: Reload the page
                        window.location.reload();
                    }, timeoutDuration);
                } else {
                    // If the user navigates away from the QR slide (e.g., clicks 'Go Back')
                    // Clear the timer so it doesn't interrupt the user.
                    clearTimeout(timeoutId);
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const QR_SLIDE_INDEX = 6; // Slide 7 is index 6
        const urlParams = new URLSearchParams(window.location.search);

        // Check if the 'thank_you=1' parameter exists in the URL
        if (urlParams.has('thank_you') && surveyCarouselEl) {
            // Remove the query parameter from the URL bar for cleanliness
            // This stops the browser from defaulting back to the QR page if the user hits refresh manually
            history.replaceState({}, document.title, window.location.pathname);

            // Force the carousel to the QR slide (index 6) immediately
            // This makes the QR slide 'active'
            setTimeout(() => {
                surveyCarousel.to(QR_SLIDE_INDEX);
            }, 100); // Small delay to ensure carousel is fully initialized
        }

        // Your existing DOMContentLoaded timer logic is below,
        // and it will now correctly fire the 3-minute timer
        // once the 'slid.bs.carousel' event is triggered by the .to(6) call.
    });
</script>
</body>
</html>
