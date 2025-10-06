<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Satisfaction Survey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @laravelPWA
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: 'Roboto', sans-serif;

            /* New dynamic background properties */
            background: #f8f9fa; /* Fallback color */
            background-image: url("{{ asset('storage/logo/blue5.jpg') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* This makes the background stay in place when scrolling */
            background-repeat: no-repeat;
        }
        .container {
            background: transparent;
            min-height: 100vh;
        }
        h2, h3, h6, p{
            text-align: center;
            padding: 0;
            margin: 0;
        }
        .center-logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 20px;
        }
        .center-logo-container img{
            max-width: 300px;
            height: auto;
        }
        .carousel-item {
            min-height: 400px;
        }
        .carousel-control-prev, .carousel-control-next {
            display: none;
        }
        .question-slide {
            padding: 50px 10px 30px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .carousel-indicators [data-bs-target] {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #007bff;
        }
        .staff-avatar {
            width: 150px;
            height: 150px;
            border: 2px solid black;
            cursor: pointer;
        }
        .staff-item {
            margin: 20px;
        }
        .emoji-icon {
            font-size: 5rem;
        }
        p {
            text-align: center;
        }

        /* Container (the visible box) */
        .select2-container .select2-selection--single {
            height: 50px !important;     /* your desired height */
            display: flex;
            align-items: center;         /* vertically center text */
            font-size: 16px;             /* optional: increase font */
        }

        /* The text inside */
        .select2-container .select2-selection__rendered {
            line-height: 48px !important; /* must match container height - 2px */
        }

        /* The arrow dropdown icon */
        .select2-container .select2-selection__arrow {
            height: 48px !important;    /* match container */
        }

        /* Add these new styles to your existing <style> block */
        .staff-carousel-container {
            position: relative;
            width: 100%;
            max-width: 90%; /* Adjust as needed, leaves space for arrows */
            margin: 0 auto;
            display: flex;
            align-items: center;
        }

        .staff-scroll-panel {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory; /* Smooth snapping effect */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
            scrollbar-width: none; /* Hide scrollbar for Firefox */
        }

        .staff-scroll-panel::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
        }

        .staff-item {
            flex: 0 0 auto; /* Prevents items from shrinking */
            margin: 20px;
            scroll-snap-align: center; /* Center items when snapping */
        }

        .staff-avatar {
            width: 150px;
            height: 150px;
            border: 3px solid transparent; /* Start with a transparent border */
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        /* Style for the selected staff member's avatar */
        .staff-avatar.selected {
            border-color: #007bff; /* Highlight color */
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .scroll-arrow svg {
            width: 24px;
            height: 24px;
            fill: #333;
        }

        .scroll-arrow.left-arrow {
            left: -50px; /* Position outside the container */
        }

        .scroll-arrow.right-arrow {
            right: -50px; /* Position outside the container */
        }

        /* Responsive: Hide arrows on mobile where swipe is natural */
        @media (max-width: 768px) {
            .scroll-arrow {
                display: none;
            }
            .staff-carousel-container {
                max-width: 100%; /* Use full width on mobile */
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="center-logo-container">
        <image src="{{ asset('storage/logo/logo-with-seals.png') }}">
    </div>
    <h6 style="padding-top: ;">HUMAN RESOURCE DEVELOPMENT OFFICE</h6>
    <h2>Customer Satisfaction Survey</h2>

    @if(session('success'))
        <div id="success-message" class="alert alert-success" role="alert"
            style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('survey.submit') }}" method="POST">
        @csrf

        <div id="surveyCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
            <div class="carousel-inner">
                {{-- <div class="carousel-item active">
                    <div class="question-slide">
                        <h3>1. Date:</h3>
                        <input type="date" name="submission_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required onchange="nextSlide()">
                        <button type="button" class="btn btn-secondary mt-3" onclick="prevSlide()" style="display:none;">Go Back</button>
                    </div>
                </div> --}}

                <div class="carousel-item active">
                    <div class="question-slide">
                        <h4 style="padding-bottom: 10px">Attended by/Inasikaso ni:</h4>
                            <div class="staff-carousel-container">
                                <button type="button" class="scroll-arrow left-arrow" aria-label="Previous">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                    </svg>
                                </button>

                                <div class="staff-scroll-panel">
                                    @foreach($staffs as $staff)
                                        <div class="text-center staff-item">
                                            <label>
                                                <input type="radio" name="user_id" value="{{ $staff->id }}" style="display:none;" required>
                                                <img src="{{ $staff->getAvatarUrl() }}" class="rounded-circle staff-avatar">
                                            </label>
                                            <br>
                                            <span class="staff-name">{{ $staff->name }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="scroll-arrow right-arrow" aria-label="Next">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                                    </svg>
                                </button>
                            </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="question-slide">
                        <h4 style="padding-bottom: 20px">Service/s Received/ Serbisyong Natanggap:</h4>
                        <select id="serviceSelect" name="problem_category_id" class="form-select form-select-lg" required>
                            <option value="">Select a service...</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->category_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary mt-3" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="question-slide">
                        <h4>RESPONSIVENESS (PAGTUGON)</h4>
                        <p>Willingness to help, assist, and provide prompt service</p>
                        <p>(Handang tumugon at magbigay nang mabilis na serbisyo)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Very Dissatisfied')">
                                <input type="radio" name="responsiveness_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                Very Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Dissatisfied')">
                                <input type="radio" name="responsiveness_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Satisfied')">
                                <input type="radio" name="responsiveness_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                Satisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'responsiveness_rating', 'Very Satisfied')">
                                <input type="radio" name="responsiveness_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                Very Satisfied
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="question-slide">
                        <h4>TIMELINESS (BILIS NG PAGTUGON)</h4>
                        <p>Satisfaction with the timeliness of service/response to your needs</p>
                        <p>(Kontento sa bilis ng serbisyo/pagtugon sa iyong pangangailangan)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Very Dissatisfied')">
                                <input type="radio" name="timeliness_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                Very Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Dissatisfied')">
                                <input type="radio" name="timeliness_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Satisfied')">
                                <input type="radio" name="timeliness_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                Satisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'timeliness_rating', 'Very Satisfied')">
                                <input type="radio" name="timeliness_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                Very Satisfied
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="question-slide">
                        <h4>COMMUNICATION (PAKIKIPAG-USAP)</h4>
                        <p>Act of keeping citizens informed in a language they can easily understand and delivered courteously</p>
                        <p>(Paggamit ng wika na madaling maunawaan at naipahayag ng magalang)</p>
                        <br>
                        <div class="d-flex flex-wrap justify-content-center">
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Very Dissatisfied')">
                                <input type="radio" name="communication_rating" value="Very Dissatisfied" style="display:none;" required>
                                <span class="emoji-icon">😞</span>
                                Very Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Dissatisfied')">
                                <input type="radio" name="communication_rating" value="Dissatisfied" style="display:none;">
                                <span class="emoji-icon">🙁</span>
                                Dissatisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Satisfied')">
                                <input type="radio" name="communication_rating" value="Satisfied" style="display:none;">
                                <span class="emoji-icon">😊</span>
                                Satisfied
                            </label>
                            <label class="btn btn-outline-primary m-2 d-flex flex-column align-items-center" onclick="selectRating(this, 'communication_rating', 'Very Satisfied')">
                                <input type="radio" name="communication_rating" value="Very Satisfied" style="display:none;">
                                <span class="emoji-icon">😁</span>
                                Very Satisfied
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="prevSlide()">Go Back</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="question-slide">
                        <h4 style="padding-bottom: 20px">Scan the QR code to fill out the comments and suggestions form</h4>
                        {{-- <textarea name="suggestions" class="form-control" rows="3" ></textarea> --}}

                        <div class="qr-code-container d-flex justify-content-center">
                            {!! QrCode::size(180)->backgroundColor(255, 255, 255, 0)->generate('https://forms.gle/Tvmm2WmjHGNqteUD9') !!}
                        </div>

                        <br>

                        <div class="mt-3">
                            <button type="button" class="btn btn-primary me-2" onclick="prevSlide()">Go Back</button>
                            <button type="button" class="btn btn-success" onclick="submitForm()">Submit</button>
                        </div>
                    </div>
                </div>

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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const surveyCarouselEl = document.getElementById('surveyCarousel');
    const surveyCarousel = new bootstrap.Carousel(surveyCarouselEl, {
        touch: false,
        interval: false
    });

    // Add this new script to your existing <script> block

    document.addEventListener('DOMContentLoaded', function () {
        const panel = document.querySelector('.staff-scroll-panel');
        const leftArrow = document.querySelector('.left-arrow');
        const rightArrow = document.querySelector('.right-arrow');
        const staffItems = document.querySelectorAll('.staff-item');

        // --- Arrow Button Functionality ---
        if (panel && leftArrow && rightArrow) {
            const scrollAmount = staffItems.length > 0 ? staffItems[0].offsetWidth + 40 : 200; // 40 is for margin (20+20)

            leftArrow.addEventListener('click', () => {
                panel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            rightArrow.addEventListener('click', () => {
                panel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        // --- Staff Selection and Highlighting ---
        staffItems.forEach(item => {
            const avatar = item.querySelector('.staff-avatar');
            const radio = item.querySelector('input[type="radio"]');

            avatar.addEventListener('click', () => {
                // Remove 'selected' class from all other avatars
                document.querySelectorAll('.staff-avatar.selected').forEach(selectedAvatar => {
                    selectedAvatar.classList.remove('selected');
                });

                // Add 'selected' class to the clicked avatar
                avatar.classList.add('selected');

                // Check the corresponding radio button
                radio.checked = true;

                // Automatically move to the next slide after a short delay
                setTimeout(() => {
                    nextSlide();
                }, 300); // 300ms delay
            });
        });
    });

    function nextSlide() {
        surveyCarousel.next();
    }

    function prevSlide() {
        surveyCarousel.prev();
    }

    function selectStaff(labelElement, staffId) {
        const radioButton = labelElement.querySelector(`input[type="radio"][value="${staffId}"]`);
        if (radioButton) {
            radioButton.checked = true;
        }
        nextSlide();
    }

    function selectRating(labelElement, name, value) {
        const radioButton = labelElement.querySelector(`input[name="${name}"][value="${value}"]`);
        if (radioButton) {
            radioButton.checked = true;
        }
        nextSlide();
    }

    // New function to handle form submission
    function submitForm() {
        const form = document.querySelector('form');
        form.submit();
    }

    // 🔹 Update indicators dynamically
    surveyCarouselEl.addEventListener('slid.bs.carousel', function (event) {
        const indicators = document.querySelectorAll('.carousel-indicators button');
        indicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            indicator.removeAttribute('aria-current');
            if (index === event.to) {
                indicator.classList.add('active');
                indicator.setAttribute('aria-current', 'true');
            }
        });
    });

    setTimeout(function () {
        let successMsg = document.getElementById('success-message');
        if (successMsg) {
            successMsg.style.transition = "opacity 0.5s ease";
            successMsg.style.opacity = "0";
            setTimeout(() => successMsg.remove(), 500); // remove after fadeout
        }
    }, 5000);

    $(document).ready(function() {
    $('#serviceSelect').select2({
        placeholder: "Select a service...",
        width: '100%'
    }).on('change', function () {
        nextSlide(); // trigger your    function
    });
});
</script>
</body>
</html>
