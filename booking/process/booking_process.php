<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    session_start();
    include '../../db_connection/db_connection.php'; // Include your database connection
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if user_id is set in session
        if (!isset($_SESSION['user_id'])) {
            echo "<script>Swal.fire({ icon: 'error', title: 'Error', text: 'User not logged in.' }).then(() => { window.location.href = '../../bws_ui/login.php'; });</script>";
            exit;
        }

        // Get the form data
        $user_id = $_SESSION['user_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $duration = $_POST['duration'];
        $payment_method = $_POST['payment_method'];
        $total_price = $_POST['total_price'];
        $service_ids = $_POST['service_ids']; // Comma-separated service IDs
    
        // Validate the form data
        if (empty($user_id) || empty($appointment_date) || empty($appointment_time) || empty($duration) || empty($payment_method) || empty($total_price) || empty($service_ids)) {
            echo "<script>Swal.fire({ icon: 'error', title: 'Error', text: 'All fields are required.' }).then(() => { window.location.href = '../../booking/booking.php'; });</script>";
            exit;
        }

        // Calculate the end time based on the duration
        $end_time = date('H:i:s', strtotime($appointment_time) + $duration * 60);

        // Check for overlapping bookings
        $overlapQuery = "SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND ((appointment_time <= ? AND ADDTIME(appointment_time, SEC_TO_TIME(duration * 60)) > ?) OR (appointment_time < ? AND ? < ADDTIME(appointment_time, SEC_TO_TIME(duration * 60))))";
        $stmt = $conn->prepare($overlapQuery);
        $stmt->bind_param('sssss', $appointment_date, $end_time, $appointment_time, $appointment_time, $end_time);
        $stmt->execute();
        $stmt->bind_result($overlapCount);
        $stmt->fetch();
        $stmt->close();

        if ($overlapCount > 0) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Someone already booked for the set date and time, please select a different date and time.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../booking/booking.php';
                });
              </script>";
            exit;
        }

        // Check if slots are available for each service
        $service_ids_array = explode(',', $service_ids);
        $slotsAvailable = true;

        foreach ($service_ids_array as $service_id) {
            $slotsQuery = "SELECT slots, status FROM services WHERE id = ?";
            $stmt = $conn->prepare($slotsQuery);
            $stmt->bind_param('i', $service_id);
            $stmt->execute();
            $stmt->bind_result($slots, $status);
            $stmt->fetch();
            $stmt->close();

            if ($slots <= 0 || $status == 'Not Available') {
                $slotsAvailable = false;
                break;
            }
        }

        if ($slotsAvailable) {
            // Insert appointment into the appointments table
            $sql = "INSERT INTO appointments (user_id, appointment_date, appointment_time, payment_method, total_price, status, duration) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('isssdi', $user_id, $appointment_date, $appointment_time, $payment_method, $total_price, $duration);

            if ($stmt->execute()) {
                $appointment_id = $stmt->insert_id;

                // Insert booked services into the booked_services table
                $sql_service = "INSERT INTO booked_services (appointment_id, service_id, price) VALUES (?, ?, ?)";
                $stmt_service = $conn->prepare($sql_service);

                foreach ($service_ids_array as $service_id) {
                    // Get the service price from the services table
                    $sql_price = "SELECT price FROM services WHERE id = ?";
                    $stmt_price = $conn->prepare($sql_price);
                    $stmt_price->bind_param('i', $service_id);
                    $stmt_price->execute();
                    $stmt_price->bind_result($service_price);
                    $stmt_price->fetch();
                    $stmt_price->close();

                    // Check if the service price is successfully fetched before binding
                    if (isset($service_price)) {
                        // Insert into booked_services table with the correct price
                        $stmt_service->bind_param('iid', $appointment_id, $service_id, $service_price);
                        $stmt_service->execute();
                    } else {
                        echo "<script>Swal.fire({ icon: 'error', title: 'Error', text: 'Price for service ID $service_id not found.' }).then(() => { window.location.href = '../../booking/booking.php'; });</script>";
                        exit; // Stop further execution if price is not found
                    }

                    // Update the slots
                    $updateSlotsQuery = "UPDATE services SET slots = slots - 1 WHERE id = ?";
                    $stmt_update_slots = $conn->prepare($updateSlotsQuery);
                    $stmt_update_slots->bind_param('i', $service_id);
                    $stmt_update_slots->execute();
                    $stmt_update_slots->close();

                    // Check if slots have reached 0 and update status if necessary
                    $checkSlotsQuery = "SELECT slots FROM services WHERE id = ?";
                    $stmt_check_slots = $conn->prepare($checkSlotsQuery);
                    $stmt_check_slots->bind_param('i', $service_id);
                    $stmt_check_slots->execute();
                    $stmt_check_slots->bind_result($remainingSlots);
                    $stmt_check_slots->fetch();
                    $stmt_check_slots->close();

                    if ($remainingSlots <= 0) {
                        $updateStatusQuery = "UPDATE services SET status = 'Not Available' WHERE id = ?";
                        $stmt_update_status = $conn->prepare($updateStatusQuery);
                        $stmt_update_status->bind_param('i', $service_id);
                        $stmt_update_status->execute();
                        $stmt_update_status->close();
                    }
                }

                // Display success message with SweetAlert and redirect
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Appointment booked successfully!',
                        text: 'Please wait for approving your appointment.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '../../index.php';
                    });
                  </script>";
            } else {
                echo "<script>Swal.fire({ icon: 'error', title: 'Error', text: '" . $stmt->error . "' }).then(() => { window.location.href = '../../booking/booking.php'; });</script>";
            }

            $stmt->close();
        } else {
            echo "<script>Swal.fire({ icon: 'error', title: 'Error', text: 'No slots available or service not available for one or more selected services.' }).then(() => { window.location.href = '../../booking/booking.php'; });</script>";
        }

        $conn->close();
    } else {
        echo "<script>Swal.fire({ icon: 'error', title: 'Invalid request', text: 'Invalid request.' }).then(() => { window.location.href = '../../booking/booking.php'; });</script>";
    }
    ?>
</body>

</html>