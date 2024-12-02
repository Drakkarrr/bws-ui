<?php
session_start();
include '../../bws_ui/db_connection/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/log_in.php");
    exit;
}

// Get the appointment ID from the query string
$appointmentId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$appointmentId) {
    echo "Invalid appointment ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .cancel-reason-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .cancel-reason-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .cancel-reason-card h2 {
            margin-bottom: 20px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="cancel-reason-container">
        <div class="cancel-reason-card">
            <h2>Cancel Booking</h2>
            <form id="cancelReasonForm">
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="Please provide a reason for cancellation..."></textarea>
                </div>
                <input type="hidden" id="cancelAppointmentId" value="<?php echo htmlspecialchars($appointmentId); ?>">
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" onclick="goBackToHistory()">Back to History</button>
                    <button type="button" class="btn btn-primary" onclick="submitCancelReason()">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Booking Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="resultModalBody">
                    <!-- Message will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitCancelReason() {
            var appointmentId = document.getElementById('cancelAppointmentId').value;
            var reason = document.getElementById('cancelReason').value;

            fetch("../booking/process/cancel_booking_process.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id: appointmentId, reason: reason }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showModal("Booking cancellation successful.");
                } else {
                    showModal("Failed to cancel booking: " + data.error);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showModal("An error occurred while canceling the booking.");
            });
        }

        function showModal(message) {
            document.getElementById('resultModalBody').innerText = message;
            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            resultModal.show();
            setTimeout(() => {
                window.location.href = 'history.php';
            }, 3000);
        }

        function goBackToHistory() {
            window.location.href = 'history.php';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>