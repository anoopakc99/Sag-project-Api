<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIT Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>AIT Registration</h2>
        <form id="registrationForm">
            <div class="row g-3">
                <!-- Mobile No -->
                <div class="col-md-6">
                    <label for="mobile_no" class="form-label">Mobile No. *</label>
                    <input type="text" id="mobile_no" name="mobile_no" class="form-control" required>
                </div>

                <!-- WhatsApp No -->
                <div class="col-md-6">
                    <label for="whatsapp_no" class="form-label">What's App No. *</label>
                    <input type="text" id="whatsapp_no" name="whatsapp_no" class="form-control" required>
                </div>

                <!-- State -->
                <div class="col-md-6">
                    <label for="state" class="form-label">State *</label>
                    <select id="state" name="state" class="form-select" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- District -->
                <div class="col-md-6">
                    <label for="district" class="form-label">District *</label>
                    <select id="district" name="district" class="form-select" required>
                        <option value="">Select District</option>
                    </select>
                </div>

                <!-- Sales Person -->
                <div class="col-md-6">
                    <label for="sales_person" class="form-label">Sales Person *</label>
                    <select id="sales_person" name="sales_person" class="form-select" required>
                        <option value="">Select Sales Person</option>
                    </select>
                </div>

                <!-- Tehsil -->
                <div class="col-md-6">
                    <label for="tehsil" class="form-label">Tehsil *</label>
                    <select id="tehsil" name="tehsil" class="form-select" required>
                        <option value="">Select Tehsil</option>
                    </select>
                </div>

                <!-- Other Fields -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" class="form-control">
                </div>

                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            // Fetch districts based on state
            $('#state').change(function () {
                const stateId = $(this).val();
                $('#district').empty().append('<option value="">Select District</option>');
                $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
                
                if (stateId) {
                    $.get('/get-districts', { state_id: stateId }, function (data) {
                        if (data.length > 0) {
                            data.forEach(district => {
                                $('#district').append(`<option value="${district.id}">${district.district_name}</option>`);
                            });
                        } else {
                            $('#district').append('<option>No districts found</option>');
                        }
                    }).fail(function () {
                        $('#district').append('<option>Error fetching data</option>');
                    });
                }
            });
    
            // Fetch salespersons based on district
            $('#district').change(function () {
                const stateId = $('#state').val();
                const districtId = $(this).val();
                $('#sales_person').empty().append('<option value="">Select Sales Person</option>');
                
                if (stateId && districtId) {
                    $.get('/get-sales-persons', { state_id: stateId, district_id: districtId }, function (data) {
                        if (data.length > 0) {
                            data.forEach(person => {
                                $('#sales_person').append(`<option value="${person.id}">${person.name}</option>`);
                            });
                        } else {
                            $('#sales_person').append('<option>No salespersons found</option>');
                        }
                    }).fail(function () {
                        $('#sales_person').append('<option>Error fetching data</option>');
                    });
                }
            });
        });
    
    

        // Fetch tehsils based on district
        // $('#district').change(function () {
        //     const districtId = $(this).val();
        //     $('#tehsil').empty().append('<option value="">Select Tehsil</option>');
        //     if (districtId) {
        //         $.get('/get-tehsils', { district_id: districtId }, function (data) {
        //             data.forEach(tehsil => {
        //                 $('#tehsil').append(`<option value="${tehsil.id}">${tehsil.name}</option>`);
        //             });
        //         });
        //     }
        // });

        // Submit form
        function submitForm() {
            const formData = $('#registrationForm').serialize();
            $.post('/submit-ait-registration', formData, function (data) {
                if (data.login_id) {
                    alert(`Registration Successful! Login ID: ${data.login_id}`);
                } else {
                    alert('Registration Failed!');
                }
            });
        }
    </script>
</body>
</html>
