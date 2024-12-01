<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #d1c4e9;
            color: #4a148c;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
        }
        .btn-primary {
            background-color: #7e57c2;
            border: none;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #5e35b1;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: 0.5em;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Forgot Password</h2>
        </div>
        <div class="card-body">
            <form id="forgotPasswordForm" action="send_verification.php" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="phone">Enter your phone number:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+63</span>
                        </div>
                        <input type="text" id="phone" name="phone" class="form-control" required pattern="\d{10}" maxlength="10">
                    </div>
                    <div id="phoneError" class="error-message"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Verification Code</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function validateForm() {
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phoneError');
            const phonePattern = /^\d{10}$/;

            if (!phonePattern.test(phoneInput.value)) {
                phoneError.textContent = 'Please enter a valid phone number (10 digits).';
                return false;
            }

            phoneError.textContent = '';
            return true;
        }
    </script>
</body>
</html>