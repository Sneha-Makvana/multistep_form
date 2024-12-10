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

        <form id="multistepForm" enctype="multipart/form-data">
            <!-- Step 1: Basic Information -->
            <div class="step active" id="step1">
                <h4>Basic Information</h4>

                <!-- Hidden Field for User ID (Only if editing an existing user) -->
                <input type="hidden" id="id" name="id" value="<?= isset($user) ? $user['id'] : '' ?>">

                <!-- Full Name Field -->
                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= isset($user) ? $user['full_name'] : '' ?>" required>

                <!-- Email Field -->
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= isset($user) ? $user['email'] : '' ?>" required>

                <!-- Password Field (Optional) -->
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?= isset($user) ? $user['password'] : '' ?>">

                <!-- Gender Field -->
                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male" <?= isset($user) && $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= isset($user) && $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= isset($user) && $user['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
                </select>

                <!-- Submit/Next Button -->
                <button type="button" class="btn btn-primary" id="next1">Next</button>
            </div>

            <!-- Step 2: Interests -->
            <div class="step" id="step2">
                <h4>Interests</h4>
                <label for="interests" class="form-label">Select Interests</label>
                <div>
                    <input type="checkbox" id="interest1" name="interests[]" value="tech" <?= isset($user) && in_array('tech', explode(',', $user['interests'])) ? 'checked' : '' ?>> Tech
                    <input type="checkbox" id="interest2" name="interests[]" value="sports" <?= isset($user) && in_array('sports', explode(',', $user['interests'])) ? 'checked' : '' ?>> Sports
                    <input type="checkbox" id="interest3" name="interests[]" value="music" <?= isset($user) && in_array('music', explode(',', $user['interests'])) ? 'checked' : '' ?>> Music
                </div>

                <!-- Submit/Next Button -->
                <button type="button" class="btn btn-primary" id="next2">Next</button>
            </div>

            <!-- Step 3: Resume -->
            <div class="step" id="step3">
                <h4>Upload Resume</h4>
                <label for="resume" class="form-label">Resume (PDF, DOC, DOCX)</label>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">

                <!-- Resume Field Pre-population -->
                <?php if (isset($user['resume']) && $user['resume']) { ?>
                    <p>Current Resume: <a href="/uploads/<?= $user['resume'] ?>" target="_blank">View</a></p>
                <?php } ?>

                <!-- Submit/Next Button -->
                <button type="button" class="btn btn-primary" id="submitForm">Submit</button>
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

            // Validate the form here (optional step based on your requirements)
            validateStep(3, function(isValid) {
                if (isValid) {
                    let formData = new FormData($('#multistepForm')[0]);

                    // Send the form data to the server using AJAX
                    $.ajax({
                        url: '/user/save', // Assuming this is the route to handle both insert and update
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                alert(response.message);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>