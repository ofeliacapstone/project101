<?php
include_once "admin_dormitory_header.php";
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
include 'config.php';

// Ensure uploads directory exists
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Fetch rooms from the database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
$rooms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

// Predefined list of common amenities
$commonAmenities = ["Wi-Fi", "Air Conditioning", "Television", "Refrigerator", "Microwave", "Desk", "Closet", "Private Bathroom", "Shared Bathroom", "Laundry Facilities"];

// Handle room actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_room'])) {
        if (!empty($_POST['room_name']) && !empty($_POST['total_beds']) && !empty($_POST['price_per_month'])) {
            $imagePath = "dorm.jpg";
            if (!empty($_FILES['room_image']['name'])) {
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES['room_image']['name']);
                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    error_log("Failed to upload image.");
                }
            }

            $roomName = $_POST['room_name'];
            $totalBeds = intval($_POST['total_beds']);
            $occupiedBeds = 0; // Define occupied_beds separately
            $pricePerMonth = floatval($_POST['price_per_month']);
            $amenities = implode(", ", array_filter($_POST['amenities']));

            $stmt = $conn->prepare("INSERT INTO rooms (name, total_beds, occupied_beds, price_per_month, image, amenities) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }

            if (!$stmt->bind_param("siidss", $roomName, $totalBeds, $occupiedBeds, $pricePerMonth, $imagePath, $amenities)) {
                error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }

            if (!$stmt->execute()) {
                error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            } else {
                error_log("Room added successfully.");
            }

            $stmt->close();
        }
    } elseif (isset($_POST['update_room'])) {
        foreach ($rooms as &$room) {
            if ($room['id'] == $_POST['room_id']) {
                $room['name'] = $_POST['room_name'];
                $room['total_beds'] = intval($_POST['total_beds']);
                $room['occupied_beds'] = min(intval($_POST['occupied_beds']), $room['total_beds']);
                $room['price_per_month'] = floatval($_POST['price_per_month']);
                $room['amenities'] = implode(", ", array_filter($_POST['amenities']));

                if (!empty($_FILES['room_image']['name'])) {
                    $targetDir = "uploads/";
                    $targetFile = $targetDir . basename($_FILES['room_image']['name']);
                    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $targetFile)) {
                        $room['image'] = $targetFile;
                    } else {
                        error_log("Failed to upload image.");
                    }
                }

                $stmt = $conn->prepare("UPDATE rooms SET name = ?, total_beds = ?, occupied_beds = ?, price_per_month = ?, image = ?, amenities = ? WHERE id = ?");
                if (!$stmt) {
                    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                }

                if (!$stmt->bind_param("siidssi", $room['name'], $room['total_beds'], $room['occupied_beds'], $room['price_per_month'], $room['image'], $room['amenities'], $room['id'])) {
                    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
                }

                if (!$stmt->execute()) {
                    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
                } else {
                    error_log("Room updated successfully.");
                }

                $stmt->close();
                break;
            }
        }
    } elseif (isset($_POST['delete_room'])) {
        $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        if (!$stmt->bind_param("i", $_POST['room_id'])) {
            error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            error_log("Room deleted successfully.");
        }

        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f8ff;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .modal-header, .modal-footer {
            border-bottom: 0;
            border-top: 0;
        }
        .modal-title {
            flex-grow: 1;
            text-align: center;
        }
        .btn-close {
            margin-left: auto;
        }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 18px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        header {
            background-color: #003366;
            color: white;
            border-radius: 10px;
        }
        header h1 {
            font-size: 2.5rem;
            margin: 0;
            color: #FFD700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>
    <header class="py-3">
        <div class="container">
            <h1 class="text-center">Room Management</h1>
        </div>
    </header>
    <div class="container mt-5">
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addRoomModal" title="Add Room"><i class="fas fa-plus"></i></button>
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?php echo $room['image']; ?>" class="card-img-top" alt="Room Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $room['name']; ?></h5>
                            <p class="card-text">Occupied: <?php echo $room['occupied_beds'] . '/' . $room['total_beds']; ?></p>
                            <p class="card-text">Price per Month: ₱<?php echo $room['price_per_month']; ?></p>
                            <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#viewRoomModal<?php echo $room['id']; ?>" title="View Room"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#editRoomModal<?php echo $room['id']; ?>" title="Edit Room"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                <button type="submit" name="delete_room" class="btn btn-danger btn-icon" title="Delete Room" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- View Room Modal -->
                <div class="modal fade" id="viewRoomModal<?php echo $room['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Room Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12 text-left mb-3" style="margin-left: 100px;" class="ms-5">
                                        <h4 class="text-primary"><?php echo htmlspecialchars($room['name']); ?></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <img src="<?php echo $room['image']; ?>" class="img-fluid rounded shadow-sm mb-3" alt="Room Image">
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Total Beds:</strong> <?php echo $room['total_beds']; ?></li>
                                            <li class="list-group-item"><strong>Occupied Beds:</strong> <?php echo max(0, min($room['occupied_beds'], $room['total_beds'])); ?></li>
                                            <li class="list-group-item"><strong>Available Beds:</strong> <?php echo max(0, $room['total_beds'] - $room['occupied_beds']); ?></li>
                                            <li class="list-group-item"><strong>Price per Month:</strong> ₱<?php echo number_format($room['price_per_month'], 2); ?></li>
                                            <li class="list-group-item"><strong>Amenities:</strong> <?php echo htmlspecialchars($room['amenities'] ?? 'Not specified'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>  
                         

                <!-- Edit Room Modal -->
                <div class="modal fade" id="editRoomModal<?php echo $room['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Room</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Room Name</label>
                                        <input type="text" name="room_name" value="<?php echo $room['name']; ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Beds</label>
                                        <input type="number" name="total_beds" value="<?php echo $room['total_beds']; ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Occupied Beds</label>
                                        <input type="number" name="occupied_beds" value="<?php echo $room['occupied_beds']; ?>" class="form-control" required min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price per Month</label>
                                        <input type="number" step="0.01" name="price_per_month" value="<?php echo $room['price_per_month']; ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Room Image</label>
                                        <input type="file" name="room_image" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Amenities</label>
                                        <div id="editAmenitiesContainer<?php echo $room['id']; ?>">
                                            <?php
                                            $amenities = explode(", ", $room['amenities']);
                                            foreach ($commonAmenities as $amenity): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity); ?>" id="edit_amenity_<?php echo $amenity; ?>_<?php echo $room['id']; ?>" <?php echo in_array($amenity, $amenities) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="edit_amenity_<?php echo $amenity; ?>_<?php echo $room['id']; ?>">
                                                        <?php echo htmlspecialchars($amenity); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <button type="submit" name="update_room" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Room Modal -->
        <div class="modal fade" id="addRoomModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Room Name</label>
                                <input type="text" name="room_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Beds</label>
                                <input type="number" name="total_beds" class="form-control" required min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price per Month (₱)</label>
                                <input type="number" step="0.01" name="price_per_month" class="form-control" required min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Room Image</label>
                                <input type="file" name="room_image" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amenities</label>
                                <div id="addAmenitiesContainer">
                                    <?php foreach ($commonAmenities as $amenity): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity); ?>" id="add_amenity_<?php echo $amenity; ?>">
                                            <label class="form-check-label" for="add_amenity_<?php echo $amenity; ?>">
                                                <?php echo htmlspecialchars($amenity); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" name="add_room" class="btn btn-success">Add Room</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>