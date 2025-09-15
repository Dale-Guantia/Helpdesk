<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Satisfaction Survey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
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
            padding: 60px;
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
                        <div class="d-flex flex-wrap justify-content-center">
                            @foreach($staffs as $staff)
                                <div class="text-center staff-item">
                                    <label>
                                        <input type="radio" name="user_id" value="{{ $staff->id }}" style="display:none;" required>
                                        <img src="{{ $staff->getAvatarUrl() }}" class="rounded-circle staff-avatar" onclick="selectStaff(this, '{{ $staff->id }}')">
                                    </label>
                                    <br>
                                    <span class="staff-name">{{ $staff->name }}</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- <button type="button" class="btn btn-secondary mt-3" onclick="prevSlide()" style="display:none;">Go Back</button> --}}
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

    function openFullscreen() {
        const element = document.documentElement;

        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { /* Firefox */
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) { /* IE/Edge */
            element.msRequestFullscreen();
        }
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        openFullscreen();
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

if ('serviceWorker' in navigator) {
navigator.serviceWorker.register('/sw.js')
    .then(reg => console.log("✅ Service Worker registered:", reg))
    .catch(err => console.error("❌ SW registration failed:", err));
}
</script>
</body>
</html>
