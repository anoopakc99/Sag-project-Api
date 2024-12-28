<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIT Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            }
    
            .form-header {
                text-align: center;
                margin-bottom: 2rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #e2e8f0;
            }
    
            .form-header h2 {
                color: var(--primary-color);
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
    
            .form-header p {
                color: #64748b;
                margin: 0;
            }
    
            .form-section {
                background: #f8fafc;
                padding: 1.5rem;
                border-radius: var(--border-radius);
                margin-bottom: 1.5rem;
            }
    
            .form-label {
                font-weight: 500;
                color: #334155;
                margin-bottom: 0.5rem;
            }
    
            .form-control, .form-select {
                border: 1px solid #e2e8f0;
                border-radius: var(--border-radius);
                padding: 0.75rem;
                transition: all 0.2s ease;
            }
    
            .form-control:focus, .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }
    
            .required-field::after {
                content: " *";
                color: #ef4444;
            }
    
            .btn-submit {
                background-color: var(--primary-color);
                color: white;
                padding: 0.75rem 2rem;
                border: none;
                border-radius: var(--border-radius);
                font-weight: 500;
                transition: all 0.2s ease;
                width: 100%;
            }
    
            .btn-submit:hover {
                background-color: var(--secondary-color);
                transform: translateY(-1px);
            }
    
            .icon-input {
                position: relative;
            }
    
            .icon-input i {
                position: absolute;
                top: 50%;
                left: 1rem;
                transform: translateY(-50%);
                color: #64748b;
            }
    
            .icon-input input {
                padding-left: 2.5rem;
            }
    
            @media (max-width: 768px) {
                .registration-container {
                    margin: 1rem;
                    padding: 1rem;
                }
    
                .form-section {
                    padding: 1rem;
                }
            }
    
            /* Loading spinner */
            .spinner {
                display: none;
                width: 1.5rem;
                height: 1.5rem;
                border: 3px solid #f3f3f3;
                border-top: 3px solid var(--primary-color);
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto;
            }
    
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
    
            /* Success message */
            .alert {
                display: none;
                margin-top: 1rem;
            }
        </style>
    </head>
    <body>
        <div class="registration-container">
            <div class="form-header">
                <h2>AIT Registration</h2>
                <p>Please fill in your details to complete the registration</p>
            </div>
    
            <form id="registrationForm" action="{{ route('submit-ait-registration') }}" method="POST">
                @csrf
                <div class="form-section">
                    <h4 class="mb-3">Contact Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="mobile_no" class="form-label required-field">Phone Number</label>
                            <div class="icon-input">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="mobile_no" name="mobile_no" class="form-control" placeholder="Enter your phone number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="whatsapp_no" class="form-label required-field">WhatsApp Number</label>
                            <div class="icon-input">
                                <i class="fab fa-whatsapp"></i>
                                <input type="tel" id="whatsapp_no" name="whatsapp_no" class="form-control" placeholder="Enter your WhatsApp number" required>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Location Information Section -->
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
                      

                             <!-- Personal Information Section -->
                <div class="form-section">
                    <h4 class="mb-3">Personal Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label required-field">Full Name</label>
                            <div class="icon-input">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Email Address</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Address</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="address" name="address" class="form-control" placeholder="Enter your address" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Brand</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="text" name="brand" class="form-control" placeholder="Enter Brand" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Breed</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="text" name="brand" class="form-control" placeholder="Enter Breed" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Monthely AI</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="text" name="brand" class="form-control" placeholder="Enter Monthely AI" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Container Type</label>
                            <div class="icon-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="text" name="brand" class="form-control" placeholder="Enter Container Type1 Type2 Type3" required>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-top: 24px;">
                           
                            <button type="submit" class="btn-submit mt-4" onclick="submitForm(event)">
                        </div>
                    </div>
                </div>
              
               
    
                <div class="spinner" id="submitSpinner"></div>
                <div class="alert alert-success" id="successMessage"></div>
                <div class="alert alert-danger" id="errorMessage"></div>
                
               
                
            </form>
        </div>
    

 
    <script>
        $(document).ready(function () {
            // Fetch districts based on state
            $('#state').change(function () {
                const stateId = $(this).val();
                $('#district').empty().append('<option value="">Select District</option>');
                $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
                $('#tehsil').empty().append('<option value="">Select Tehsil</option>'); // Clear tehsil dropdown
    
                if (stateId) {
                    $.get('/get-districts', { state_id: stateId }, function (data) {
                        if (data.length > 0) {
                            data.forEach(district => {
                                $('#district').append(`<option value="${district.id}">${district.district_name}</option>`);
                            });
                        } else {
                            $('#district').append('<option>No districts found</option>');
                        }
                    }).fail(function (xhr, status, error) {
                        console.error('Error fetching districts:', error);
                        $('#district').append('<option>Error fetching data</option>');
                    });
                }
            });
    
            // Fetch salespersons and tehsils based on district
            $('#district').change(function () {
                const stateId = $('#state').val();
                const districtId = $(this).val();
                $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
                $('#tehsil').empty().append('<option value="">Select Tehsil</option>'); // Clear tehsil dropdown
    
                if (stateId && districtId) {
                    // Fetch salespersons
                    $.get('/get-sales-persons', { state_id: stateId, district_id: districtId }, function (data) {
                        if (data.length > 0) {
                            data.forEach(person => {
                                $('#sales_person').append(`<option value="${person.id}">${person.name}</option>`);
                            });
                        } else {
                            $('#sales_person').append('<option>No salespersons found</option>');
                        }
                    }).fail(function (xhr, status, error) {
                        console.error('Error fetching sales persons:', error);
                        $('#sales_person').append('<option>Error fetching data</option>');
                    });
    
                    // Fetch tehsils
                    $.get('/get-tehsils', { district_id: districtId }, function (data) {
                        if (data.length > 0) {
                            data.forEach(tehsil => {
                                $('#tehsil').append(`<option value="${tehsil.id}">${tehsil.name}</option>`);
                            });
                        } else {
                            $('#tehsil').append('<option>No tehsils found</option>');
                        }
                    }).fail(function (xhr, status, error) {
                        console.error('Error fetching tehsils:', error);
                        $('#tehsil').append('<option>Error fetching data</option>');
                    });
                }
            });
        });
  
    });
    </script>
    
</body>
</html>
