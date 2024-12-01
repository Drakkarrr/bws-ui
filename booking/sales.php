<?php
session_start();
include_once '../../bws_ui/includes/header.php';
include_once '../../bws_ui/db_connection/db_connection.php';

// Fetch sales data from multiple tables
$query = "
    SELECT 
        s.name AS service_name, 
        COUNT(bs.service_id) AS total_bookings, 
        SUM(bs.price) AS total_sales 
    FROM services s 
    JOIN booked_services bs ON s.id = bs.service_id
    JOIN complete_bookings cb ON bs.appointment_id = cb.appointment_id
    GROUP BY s.name";
$result = $conn->query($query);

$services = [];
$totalBookings = [];
$totalSales = [];
$totalSalesAmount = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row['service_name'];
        $totalBookings[] = $row['total_bookings'];
        $totalSales[] = $row['total_sales'];
        $totalSalesAmount += $row['total_sales'];
    }
} else {
    $services = ['No Data'];
    $totalBookings = [0];
    $totalSales = [0];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Navbar Section -->
    <header class="navbar bg-light shadow-sm">
        <div class="container d-flex align-items-center">
            <!-- Sidebar Toggle Button -->
            <button class="sidebar-toggle btn btn-outline-danger me-3" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Logo (Centered) -->
            <a href="dashboard.php" class="navbar-brand mx-auto text-dark">Bernadette Wellness Spa Admin Panel</a>
        </div>
    </header>

    <!-- Sidebar Section -->
    <div class="sidebar bg-light shadow-lg p-3 mb-5 rounded" id="sidebar">
        <button class="close-btn btn btn-outline-purple mb-4" onclick="toggleSidebar()" style="font-size: 1.5rem; position: absolute; right: 15px; top: 15px;">&times;</button>
        <ul class="list-unstyled">
            <li class="mb-3">
                <a href="../dashboard.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-tachometer-alt fa-lg me-3 icon"></i> <!-- Dashboard Icon -->
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../login/process/view_all_users.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Registered Users Icon -->
                    <span>Registered Users</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="#" id="manageBookingsLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item shadow rounded p-3 bg-white" role="button" aria-expanded="false">
                    <i class="fas fa-calendar-alt fa-lg me-3 icon text-primary"></i>
                    <span class="fw-bold">Manage Bookings</span>
                    <i class="fas fa-chevron-down ms-auto toggle-icon text-secondary"></i>
                </a>
                <ul class="list-unstyled ms-4 mt-2 collapse" id="manageBookingsDropdown">
                    <li class="mb-2">
                        <a href="../booking/pending_booking.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-warning"></i> Pending Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/cancelled_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-times-circle me-2 text-danger"></i> Cancelled Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/no-show_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-user-slash me-2 text-muted"></i> No-Show Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/approved_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-check-circle me-2 text-success"></i> Approved Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/sales.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-dollar-sign me-2 text-info"></i> Sales
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/complete_bookings.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-calendar-check me-2 text-primary"></i> Complete Bookings
                        </a>
                    </li>
                </ul>
            </li>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const manageBookingsLink = document.getElementById('manageBookingsLink');
                    const manageBookingsDropdown = document.getElementById('manageBookingsDropdown');
                    const toggleIcon = manageBookingsLink.querySelector('.toggle-icon');

                    manageBookingsLink.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent default anchor click behavior

                        // Toggle the 'show' class to open/close the dropdown
                        const isExpanded = manageBookingsDropdown.classList.toggle('show');
                        manageBookingsLink.setAttribute('aria-expanded', isExpanded);

                        // Toggle the icon between chevron-down and chevron-up
                        toggleIcon.classList.toggle('fa-chevron-down', !isExpanded);
                        toggleIcon.classList.toggle('fa-chevron-up', isExpanded);
                    });
                });
            </script>

            <li class="mb-3">
                <a href="../add_category.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-tags fa-lg me-3 icon"></i> <!-- Add Service Category Icon -->
                    <span>Add Service Category</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../add_services.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-plus-circle fa-lg me-3 icon"></i> <!-- Add Services Icon -->
                    <span>Add Services</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../reminders/reminders.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-bell fa-lg me-3 icon"></i> <!-- Reminder Icon -->
                    <span>Appointment Reminders</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../staff/manage_staff.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Staff Icon -->
                    <span>Manage Staff</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item" id="logoutLink">
                    <i class="fas fa-sign-out-alt fa-lg me-3 icon"></i> <!-- Log Out Icon -->
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0">Sales Report</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                        <div class="mt-4">
                            <h5 class="text-center">Total Sales: ₱<?php echo number_format($totalSalesAmount, 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($services); ?>,
                    datasets: [{
                        label: 'Total Bookings',
                        data: <?php echo json_encode($totalBookings); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Total Sales (₱)',
                        data: <?php echo json_encode($totalSales); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-US', {
                                            style: 'currency',
                                            currency: 'PHP'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount (₱)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Services'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>

<style>
    #sidebar {
        position: fixed;
        left: -250px;
        top: 0;
        height: 100%;
        width: 250px;
        background: #f8f9fa;
        transition: left 0.3s;
        z-index: 1000;
    }

    #sidebar.active {
        left: 0;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .sidebar-overlay.active {
        display: block;
    }
</style>