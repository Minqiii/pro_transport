<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require 'conDB.php';
    if (isset($_SESSION['user_id'])) {
        require 'nav_user.php'; // เปลี่ยนตามตำแหน่งที่ตั้งของไฟล์นำทางของคุณ
    } else {
        require 'nav.php';
    }

    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/transport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdzN1yX1RIwyB3ASi9WUuOw-n52OcW4Tc&callback=initAutocomplete&libraries=places"></script>
    <script src="./script/transport.js"></script>

    <title>Delivery Tracking</title>
</head>


<body>
    <div id="container">
        <div id="sidebar">
            <h1>Deliveree</h1>
            <nav class="nav-steps">
                <a href="step1.php" class="step active">1.เส้นทาง</a>
                <a href="step2.php" class="step2">2.บริการ</a>
                <a href="step3.php" class="step3">3.ข้อมูลการจอง</a>
            </nav>
        </div>

        <div id="content"></div>
        <div id="map" style="width: 100%; height: 100%;"></div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const steps = document.querySelectorAll(".nav-steps .step");

            steps.forEach(step => {
                step.addEventListener("click", function (event) {
                    event.preventDefault();
                    // Remove 'active' class from all steps
                    steps.forEach(s => s.classList.remove("active"));
                    // Add 'active' class to the clicked step
                    this.classList.add("active");

                    // Load content
                    const stepUrl = this.getAttribute("href");
                    loadNextStep(stepUrl);
                });
            });

            // Load content of step1 initially
            loadNextStep("step1.php");

            // Handle form submissions
            document.getElementById("content").addEventListener("submit", function (event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                const action = form.getAttribute("action");

                fetch(action, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("content").innerHTML = data;
                        // Update the active step in the sidebar
                        document.querySelectorAll(".nav-steps .step").forEach(s => s.classList.remove("active"));
                        const activeStep = document.querySelector(`.nav-steps a[href="${action}"]`);
                        if (activeStep) {
                            activeStep.classList.add("active");
                        }
                    })
                    .catch(error => console.error('Error loading next step:', error));
            });
        });

        function loadNextStep(stepUrl) {
            fetch(stepUrl)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("content").innerHTML = data;
                    // Update the active step in the sidebar
                    document.querySelectorAll(".nav-steps .step").forEach(s => s.classList.remove("active"));
                    const activeStep = document.querySelector(`.nav-steps a[href="${stepUrl}"]`);
                    if (activeStep) {
                        activeStep.classList.add("active");
                    }
                })
                .catch(error => console.error('Error loading next step:', error));
        }

        window.onload = initAutocomplete;

        let map;
        let directionsService;
        let directionsRenderer;
        let autocompleteStart;
        let autocompleteEnd;

        function initAutocomplete() {
            // Create the map object first
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: -34.397, lng: 150.644 }, // You can adjust this center as needed
                zoom: 8
            });

            // Now instantiate the Autocomplete objects
            autocompleteStart = new google.maps.places.Autocomplete(
                document.getElementById('start'), { types: ['geocode'] });
            autocompleteEnd = new google.maps.places.Autocomplete(
                document.getElementById('end'), { types: ['geocode'] });

            // Add listeners to the Autocomplete objects
            autocompleteStart.addListener('place_changed', function () {
                fillInAddress(autocompleteStart, 'start');
            });
            autocompleteEnd.addListener('place_changed', function () {
                fillInAddress(autocompleteEnd, 'end');
            });

            // Directions service and renderer setup
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map); // Apply the map to the directionsRenderer
        }

        function fillInAddress(autocomplete, type) {
            const place = autocomplete.getPlace();
            let addressData = {
                'type': type, // Additional parameter to distinguish between start and end in PHP
                'street': '',
                'city': '',
                'state': '',
                'zip': '',
                'country': ''
            };

            for (const component of place.address_components) {
                const addressType = component.types[0];
                const val = component.long_name;
                switch (addressType) {
                    case 'street_number':
                        addressData.street = val + ' ';
                        break;
                    case 'route':
                        addressData.street += val;
                        break;
                    case 'locality':
                        addressData.city = val;
                        break;
                    case 'administrative_area_level_1':
                        addressData.state = val;
                        break;
                    case 'postal_code':
                        addressData.zip = val;
                        break;
                    case 'country':
                        addressData.country = val;
                        break;
                }
            }

            updateAddressInputs(addressData);
            saveAddressToDatabase(addressData);
        }

        function updateAddressInputs(data) {
            document.getElementById(data.type + '_street').value = data.street ? data.street : '-';
            document.getElementById(data.type + '_city').value = data.city ? data.city : '-';
            document.getElementById(data.type + '_state').value = data.state ? data.state : '-';
            document.getElementById(data.type + '_zip').value = data.zip ? data.zip : '-';
            document.getElementById(data.type + '_country').value = data.country ? data.country : '-';
            enableInputs(data.type);
        }

        function calculateAndDisplayRoute(callback) {
            const start = document.getElementById('start').value;
            const end = document.getElementById('end').value;

            directionsService.route({
                origin: start,
                destination: end,
                travelMode: 'DRIVING'
            }, function (response, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response);
                    if (response.routes.length > 0 && response.routes[0].legs.length > 0) {
                        const route = response.routes[0].legs[0];
                        const distanceText = route.distance.text;
                        const durationText = route.duration.text;
                        const distanceValue = route.distance.value; // distance in meters
                        const durationValue = route.duration.value; // duration in seconds

                        document.getElementById('outputDistance').innerHTML = 'distance: ' + distanceText;
                        document.getElementById('outputDuration').innerHTML = 'duration: ' + durationText;

                        // Update hidden inputs for form submission
                        document.getElementById('hiddenDistance').value = distanceValue;
                        document.getElementById('hiddenDuration').value = durationValue;

                        if (typeof callback === 'function') {
                            callback();
                        }
                    } else {
                        document.getElementById('outputDistance').innerHTML = 'distance: not found';
                        document.getElementById('outputDuration').innerHTML = 'duration: not found';
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                } else {
                    window.alert('Directions request failed due to ' + status);
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        document.getElementById('step2Form').addEventListener('submit', function (event) {
            event.preventDefault();

            calculateAndDisplayRoute(function () {
                // Submit the form after the route calculation is complete
                document.getElementById('step2Form').submit();
            });
        });

    </script>
</body>


</html>