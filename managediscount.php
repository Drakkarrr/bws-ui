<?php
session_start();
include_once '../bws_ui/includes/header.php';
include_once '../bws_ui/db_connection/db_connection.php';

// Fetch discounts
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT d.id, d.service_id, d.discount_percentage, d.discounted_price, d.start_time, d.end_time, s.name AS service_name 
          FROM discounts d 
          JOIN services s ON d.service_id = s.id";
$result = $conn->query($query);

$discounts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $discounts[] = $row;
    }
}
$conn->close();
?>

<div class="container">
    <h2 class="mt-4">Manage Discounts</h2>

    <a href="./discount.php">
        <button class="btn btn-success">Go Back</button>
    </a>

    <table class="table table-bordered mt-3" id="discountsTable">
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Discount Percentage</th>
                <th>Discounted Price</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($discounts)): ?>
                <?php foreach ($discounts as $discount): ?>
                    <tr id="discount-<?= $discount['id']; ?>">
                        <td class="service_name"><?= htmlspecialchars($discount['service_name']); ?></td>
                        <td class="discount_percentage"><?= htmlspecialchars($discount['discount_percentage']); ?>%</td>
                        <td class="discounted_price"><?= htmlspecialchars($discount['discounted_price']); ?></td>
                        <td class="start_time"><?= htmlspecialchars($discount['start_time']); ?></td>
                        <td class="end_time"><?= htmlspecialchars($discount['end_time']); ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-primary btn-sm edit-btn" data-id="<?= $discount['id']; ?>"
                                data-service-id="<?= $discount['service_id']; ?>"
                                data-discount-percentage="<?= $discount['discount_percentage']; ?>"
                                data-discounted-price="<?= $discount['discounted_price']; ?>"
                                data-start-time="<?= $discount['start_time']; ?>" data-end-time="<?= $discount['end_time']; ?>">
                                Edit
                            </button>
                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $discount['id']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No discounts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDiscountForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDiscountModalLabel">Edit Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="discountId">
                    <input type="hidden" name="service_id" id="serviceId">

                    <div class="mb-3">
                        <label for="discountPercentage" class="form-label">Discount Percentage</label>
                        <input type="number" class="form-control" name="discount_percentage" id="discountPercentage"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="discountedPrice" class="form-label">Discounted Price</label>
                        <input type="text" class="form-control" name="discounted_price" id="discountedPrice" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="startTime" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" name="start_time" id="startTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="endTime" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" name="end_time" id="endTime" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editBtns = document.querySelectorAll('.edit-btn');
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editDiscountModal'));
        const editForm = document.getElementById('editDiscountForm');

        // Open edit modal and populate fields
        editBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('discountId').value = this.dataset.id;
                document.getElementById('serviceId').value = this.dataset.serviceId;
                document.getElementById('discountPercentage').value = this.dataset.discountPercentage;
                document.getElementById('discountedPrice').value = this.dataset.discountedPrice;
                document.getElementById('startTime').value = this.dataset.startTime;
                document.getElementById('endTime').value = this.dataset.endTime;

                editModal.show();
            });
        });

        // Handle form submission for editing
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);

            fetch('edit_discount_process.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const id = formData.get('id');
                        const row = document.getElementById(`discount-${id}`);

                        row.querySelector('.discount_percentage').textContent = formData.get('discount_percentage') + '%';
                        row.querySelector('.discounted_price').textContent = formData.get('discounted_price');
                        row.querySelector('.start_time').textContent = formData.get('start_time');
                        row.querySelector('.end_time').textContent = formData.get('end_time');

                        editModal.hide();
                        alert('Discount updated successfully.');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Handle deletion
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;

                if (confirm('Are you sure you want to delete this discount?')) {
                    fetch('delete_discount_process.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                document.getElementById(`discount-${id}`).remove();
                                alert('Discount deleted successfully.');
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    });
</script>

<?php include_once '../bws_ui/includes/footer.php'; ?>