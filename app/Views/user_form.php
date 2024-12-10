<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multistep Form with AJAX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .step {
            display: none;
        }

        .active {
            display: block;
        }

        .progress-bar {
            width: 33.33%;
        }

        body {
            background: linear-gradient(#e66465, #9198e5);
            height: 100vh;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .error {
            color: red;
            font-size: 0.875em;
        }

        .has-error {
            border-color: #cc0000;
            background: lightgray;
        }

        .container {
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container shadow-sm w-50">
        <h2 class="mb-4 text-center">Student Registration</h2>

        <!-- Progress Bar -->
        <div class="progress mb-4">
            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 33.33%"></div>
        </div>

        <!-- Multistep Form -->
        <form id="multistepForm" enctype="multipart/form-data">
            <!-- Step 1: Basic Information -->
            <div class="step active" id="step1">
                <h4>Basic Information</h4>
                <div class="mb-3">
                    <input type="hidden" id="id" name="id" value="">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                    <div class="error" id="full_name_error"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="error" id="email_error"></div>
                </div>

                <button type="button" class="btn btn-primary" id="next1">Next</button>
            </div>

            <!-- Step 2: Preferences -->
            <div class="step" id="step2">
                <h4>Preferences</h4>
                <div class="mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <input type="radio" name="gender" value="male" id="gender" required> Male
                    <input type="radio" name="gender" value="female" id="gender"> Female
                    <div class="error" id="gender_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hobbies <span class="text-danger">*</span></label>
                    <input type="checkbox" name="interests[]" value="Sports"> Sports
                    <input type="checkbox" name="interests[]" value="Music"> Music
                    <input type="checkbox" name="interests[]" value="Movies"> Movies
                    <input type="checkbox" name="interests[]" value="Reading"> Reading
                    <div class="error" id="interests_error"></div>
                </div>
                <button type="button" class="btn btn-secondary" id="prev2">Previous</button>
                <button type="button" class="btn btn-primary" id="next2">Next</button>
            </div>

            <!-- Step 3: Additional Information -->
            <div class="step" id="step3">
                <h4>Additional Information</h4>
                <div class="mb-3">
                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                    <select class="form-select" id="country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="USA">USA</option>
                        <option value="India">India</option>
                        <option value="UK">UK</option>
                        <option value="Landon">Landon</option>
                        <option value="America">America</option>

                    </select>
                    <div class="error" id="country_error"></div>
                </div>
                <div class="mb-3">
                    <label for="resume" class="form-label">Upload Resume <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="resume" name="resume" required>
                    <div id="existingResumeContainer">
                        <label>Uploaded Resume:</label>
                        <p id="uploadedResume"></p>
                    </div>

                    <div class="error" id="resume_error"></div>
                </div>
                <button type="button" class="btn btn-secondary" id="prev3">Previous</button>
                <button type="button" class="btn btn-success" id="submitForm">Submit</button>
                <button type="button" class="btn btn-warning" id="updateForm" style="display:none;">Update</button>
                <div class="error" id="form_error" style="display: none;"></div>
                <div class="success text-success" id="form_success" style="display: none;"></div>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 1;

        function showStep(step) {
            $('.step').removeClass('active');
            $('#step' + step).addClass('active');
        }

        function updateProgress(step) {
            $('#progressBar').css('width', (step - 1) * 33.33 + '%');
        }

        function validateStep(step, callback) {
            let formData = new FormData($('#multistepForm')[0]);
            formData.append('current_step', step);

            $.ajax({
                url: '/validate-step',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        callback(true);
                    } else {
                        if (response.errors) {
                            $('.error').text('');
                            for (let field in response.errors) {
                                $('#' + field + '_error').text(response.errors[field]).show();
                                $('#' + field).addClass('has-error');
                            }
                        }
                        callback(false);
                    }
                },
                error: function() {
                    alert('An error occurred during validation.');
                    callback(false);
                }
            });
        }

        $('#next1').click(function() {
            validateStep(1, function(isValid) {
                if (isValid) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress(currentStep);
                }
            });
        });

        $('#next2').click(function() {
            validateStep(2, function(isValid) {
                if (isValid) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress(currentStep);
                }
            });
        });

        $('#prev2').click(function() {
            currentStep--;
            showStep(currentStep);
            updateProgress(currentStep);
        });

        $('#prev3').click(function() {
            currentStep--;
            showStep(currentStep);
            updateProgress(currentStep);
        });

        $('#submitForm').click(function(e) {
            e.preventDefault();

            validateStep(3, function(isValid) {
                if (isValid) {
                    let formData = new FormData($('#multistepForm')[0]);

                    $.ajax({
                        url: '/insert',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                $('#form_success').html(response.message + ' <a href="/users" class="btn btn-link">View Records</a>').show();
                                $('#form_error').hide();

                                $('#multistepForm')[0].reset();
                                $('.error').text('');
                            } else {
                                if (response.errors) {
                                    $('.error').text('');
                                    for (let field in response.errors) {
                                        $('#' + field + '_error').text(response.errors[field]).show();
                                        $('#' + field).addClass('has-error');
                                    }
                                }
                                $('#form_success').hide();
                            }
                        },
                        error: function() {
                            $('#form_error').text('An error occurred during submission.').show();
                            $('#form_success').hide();
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            const userId = new URLSearchParams(window.location.search).get('id');

            if (userId) {
                $.ajax({
                    url: `/get-user/${userId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {

                        if (response.status) {
                            const user = response.user;
                            $('#id').val(user.id);
                            $('#full_name').val(user.full_name);
                            $('#email').val(user.email);
                            $('input[name="gender"][value="' + user.gender + '"]').prop('checked', true);
                            const interests = user.interests.split(',');
                            $('input[name="interests[]"]').each(function() {
                                $(this).prop('checked', interests.includes($(this).val()));
                            });
                            $('#country').val(user.country);
                            if (user.resume) {
                                $('#resume').after(
                                    `<div id="existing_resume">
                                        <small>Current Resume: <b>${user.resume}</b></small>
                                       
                                    </div>`
                                );
                            }
                            $('#updateForm').show();
                            $('#submitForm').hide();
                        } else {
                            alert(response.message);
                        }

                    },
                    error: function() {
                        alert('An error occurred while fetching user data.');
                    }
                });
            }
        });
        $('#updateForm').click(function(e) {
            e.preventDefault();
            validateStep(3, function(isValid) {
                if (isValid) {
                    let formData = new FormData($('#multistepForm')[0]);
                    $.ajax({
                        url: '/update-user',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                $('#form_success').html(response.message + ' <a href="/users" class="btn btn-link">View Records</a>').show();
                                // $('#form_success').html(response.message).show();
                                $('#form_error').hide();
                                $('#multistepForm')[0].reset();
                                $('.error').text('');
                            } else {
                                $('#form_error').html(response.message).show();
                                $('#form_success').hide();
                            }
                        },
                        error: function() {
                            $('#form_error').text('An error occurred during the update.').show();
                            $('#form_success').hide();
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>