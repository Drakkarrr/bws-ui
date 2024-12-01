<?php
include_once '../bws_ui/db_connection/db_connection.php'; // Include your DB connection

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_all_available'])) {
        // Set all services to available
        $updateQuery = "UPDATE services SET status = 'Available'";
        if ($conn->query($updateQuery) === TRUE) {
            $message = 'All services set to available successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to set all services to available.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['set_all_unavailable'])) {
        // Set all services to unavailable
        $updateQuery = "UPDATE services SET status = 'Not Available'";
        if ($conn->query($updateQuery) === TRUE) {
            $message = 'All services set to unavailable successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to set all services to unavailable.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['add_slot_to_all'])) {
        // Add 1 slot to all services
        $updateQuery = "UPDATE services SET slots = slots + 1";
        if ($conn->query($updateQuery) === TRUE) {
            $message = 'Added 1 slot to all services successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add slot to all services.';
            $messageType = 'error';
        }
    } else {
        $serviceId = $_POST['service_id'];
        $newStatus = $_POST['status'];
        $slots = $_POST['slots'];

        // Update the status and slots of the selected service
        $updateQuery = "UPDATE services SET status = ?, slots = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        if ($stmt) {
            $stmt->bind_param('sii', $newStatus, $slots, $serviceId);
            if ($stmt->execute()) {
                $message = 'Service status and slots updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to update service status and slots.';
                $messageType = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Failed to prepare the update statement.';
            $messageType = 'error';
        }
    }
}

include_once '../bws_ui/includes/header.php';

// Fetch all services
$query = "SELECT id, name, status, slots FROM services";
$result = $conn->query($query);
$services = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
} else {
    echo "Error fetching services: " . $conn->error;
}
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Manage Service Status</h2>
    <div class="d-flex justify-content-center mb-3">
        <form method="POST" action="manage_service_status.php" class="d-inline me-2">
            <button type="submit" name="set_all_available" class="btn btn-success">Set All Services to Available</button>
        </form>
        <form method="POST" action="manage_service_status.php" class="d-inline me-2">
            <button type="submit" name="set_all_unavailable" class="btn btn-danger">Set All Services to Unavailable</button>
        </form>
        <form method="POST" action="manage_service_status.php" class="d-inline">
            <button type="submit" name="add_slot_to_all" class="btn btn-info">Add a Slot to All Services</button>
        </form>
    </div>
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 600px;">
        <form method="POST" action="manage_service_status.php">
            <div class="mb-3">
                <label for="serviceSelect" class="form-label">Select Service</label>
                <select name="service_id" class="form-select" id="serviceSelect" required>
                    <option value="" selected>Select a service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>" data-status="<?php echo $service['status']; ?>" data-slots="<?php echo $service['slots']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select" id="status" required>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="slots" class="form-label">Slots</label>
                <input type="number" name="slots" class="form-control" id="slots" required min="0">
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal HTML -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $message; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($message): ?>
        var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
    <?php endif; ?>

    // Update status and slots fields based on selected service
    document.getElementById('serviceSelect').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var status = selectedOption.getAttribute('data-status');
        var slots = selectedOption.getAttribute('data-slots');

        document.getElementById('status').value = status;
        document.getElementById('slots').value = slots;
    });
});
</script>

<?php include_once '../bws_ui/includes/footer.php'; ?>