<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIT Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f8fafc;
            --text-color: #1e293b;
            --border-radius: 8px;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .registration-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
        }

        .form-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            width: 100%;
            font-weight: 500;
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
        }

        .spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            display: none;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .registration-container {
                margin: 1rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="form-header">
            <h2>AIT Registration</h2>
            <p>Please fill in your details to complete the registration</p>
        </div>

        <div class="alert alert-success" id="successAlert" role="alert"></div>
        <div class="alert alert-danger" id="errorAlert" role="alert"></div>

        <form id="registrationForm">
            @csrf
            <!-- Contact Information -->
            <div class="form-section">
                <h4 class="mb-3">Contact Information</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="mobile_no" class="form-label required-field">Phone Number</label>
                        <input type="tel" id="mobile_no" name="mobile_no" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp_no" class="form-label required-field">WhatsApp Number</label>
                        <input type="tel" id="whatsapp_no" name="whatsapp_no" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h4 class="mb-3">Location Details</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="state" class="form-label required-field">State</label>
                        <select id="state" name="state" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="district" class="form-label required-field">District</label>
                        <select id="district" name="district" class="form-select" required>
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sales_person" class="form-label required-field">Sales Person</label>
                        <select id="sales_person" name="sales_person" class="form-select" required>
                            <option value="">Select Sales Person</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tehsil" class="form-label required-field">Tehsil</label>
                        <select id="tehsil" name="tehsil" class="form-select" required>
                            <option value="">Select Tehsil</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="form-section">
                <h4 class="mb-3">Personal Information</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label required-field">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label required-field">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label required-field">Address</label>
                        <input type="text" id="address" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="brand" class="form-label required-field">Brand</label>
                        <input type="text" id="brand" name="brand" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="breed" class="form-label required-field">Breed</label>
                        <input type="text" id="breed" name="breed" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="monthly_ai" class="form-label required-field">Monthly AI</label>
                        <input type="text" id="monthly_ai" name="monthly_ai" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="container_type" class="form-label required-field">Container Type</label>
                        <input type="text" id="container_type" name="container_type" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="spinner" id="submitSpinner"></div>
            <button type="submit" class="btn-submit">Submit Registration</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // State change handler
        $('#state').change(function() {
            const stateId = $(this).val();
            $('#district').empty().append('<option value="">Select District</option>');
            $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
            $('#tehsil').empty().append('<option value="">Select Tehsil</option>');

            if (stateId) {
                $.get('/get-districts', { state_id: stateId })
                    .done(function(data) {
                        data.forEach(function(district) {
                            $('#district').append(`<option value="${district.id}">${district.district_name}</option>`);
                        });
                    })
                    .fail(function(xhr, status, error) {
                        showError('Error loading districts. Please try again.');
                    });
            }
        });

        // District change handler
        $('#district').change(function() {
            const districtId = $(this).val();
            $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
            $('#tehsil').empty().append('<option value="">Select Tehsil</option>');

            if (districtId) {
                // Load sales persons
                $.get('/get-sales-persons', { district_id: districtId })
                    .done(function(data) {
                        data.forEach(function(person) {
                            $('#sales_person').append(`<option value="${person.id}">${person.name}</option>`);
                        });
                    })
                    .fail(function(xhr, status, error) {
                        showError('Error loading sales persons. Please try again.');
                    });

                // Load tehsils
                $.get('/get-tehsils', { district_id: districtId })
                    .done(function(data) {
                        data.forEach(function(tehsil) {
                            $('#tehsil').append(`<option value="${tehsil.id}">${tehsil.name}</option>`);
                        });
                    })
                    .fail(function(xhr, status, error) {
                        showError('Error loading tehsils. Please try again.');
                    });
            }
        });

        // Form submission
        $('#registrationForm').on('submit', function(e) {
            e.preventDefault();
            
            // Show spinner, hide alerts
            $('#submitSpinner').show();
            $('.alert').hide();
            $('.btn-submit').prop('disabled', true);

            const formData = $(this).serialize();

            $.ajax({
                url: '/submit-ait-registration',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        showSuccess(`Registration successful! Your Login ID is: ${response.login_id}`);
                        $('#registrationForm')[0].reset();
                    } else {
                        showError(response.message || 'Registration failed. Please try again.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Registration failed. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    $('#submitSpinner').hide();
                    $('.btn-submit').prop('disabled', false);
                }
            });
        });

        function showSuccess(message) {
            $('#successAlert').html(message).show();
            $('#errorAlert').hide();
            $('html, body').animate({ scrollTop: $('#successAlert').offset().top - 100 }, 'slow');
        }

        function showError(message) {
            $('#errorAlert').html(message).show();
            $('#successAlert').hide();
            $('html, body').animate({ scrollTop: $('#errorAlert').offset().top - 100 }, 'slow');
        }
    });
    </script>
</body>
</html>