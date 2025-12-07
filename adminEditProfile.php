<?php
include('connection.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: loginForm.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin data
$query = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $admin = $result->fetch_assoc();
} else {
    die("Admin not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Define fields that should not be updated
    $skip_fields = ['id', 'password', 'created_at', 'updated_at'];
    $readonly_fields = ['username']; // Fields that shouldn't be editable
    
    // Build dynamic update query
    $update_fields = [];
    $values = [];
    $types = '';
    
    foreach ($admin as $field_name => $field_value) {
        // Skip fields that shouldn't be updated
        if (in_array($field_name, $skip_fields) || in_array($field_name, $readonly_fields)) {
            continue;
        }
        
        // Check if the field exists in POST data
        if (isset($_POST[$field_name])) {
            $update_fields[] = "$field_name = ?";
            $values[] = $_POST[$field_name];
            $types .= 's'; // Assuming all fields are strings, adjust if needed
        }
    }
    
    if (!empty($update_fields)) {
        // Add admin_id for WHERE clause
        $values[] = $admin_id;
        $types .= 'i';
        
        $update_query = "UPDATE admin SET " . implode(', ', $update_fields) . " WHERE id = ?";
        
        $stmt = $conn->prepare($update_query);
        
        if ($stmt) {
            $stmt->bind_param($types, ...$values);
            
            if ($stmt->execute()) {
                echo "<script>
                    alert('Admin profile updated successfully!');
                    window.location.href = window.location.href;
                </script>";
                exit();
            } else {
                echo "<script>alert('Error updating admin profile: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('No fields to update!');</script>";
    }
    
    // Refresh admin data after update attempt
    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:slnt,wght@-10..0,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        .font-inter {
            font-family: "Inter", sans-serif;
        }

        .font-raleway {
            font-family: "Raleway", sans-serif;
        }
    </style>
</head>

<body>
    <div class="">
        <div class="drawer lg:drawer-open">
            <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content flex flex-col">
                <?php include 'navbar.php'; ?>
                <!-- Page content here -->

                <div class="flex justify-center mt-12">
                    <div class="w-full max-w-2xl bg-white border border-gray-200 rounded-lg shadow-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800">Edit Admin Profile</h2>
                            <p class="text-sm text-gray-600">Update your admin profile details below.</p>
                        </div>

                        <!-- Debug Information (Remove in production) -->
                        <div class="p-4 bg-yellow-50 border-b">
                            <details>
                                <summary class="cursor-pointer text-sm font-medium text-yellow-800">Debug Info (Click to expand)</summary>
                                <div class="mt-2 text-xs">
                                    <p><strong>Admin ID:</strong> <?php echo $admin_id; ?></p>
                                    <p><strong>POST Data:</strong> <?php echo $_SERVER['REQUEST_METHOD'] === 'POST' ? 'Form submitted' : 'No form submission'; ?></p>
                                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                                        <p><strong>Form Fields:</strong> <?php print_r(array_keys($_POST)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </details>
                        </div>

                        <form method="POST" class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php
                                // Dynamic form generation based on admin table fields
                                $skip_fields = ['id', 'password', 'created_at', 'updated_at'];
                                $readonly_fields = ['username']; // Fields that shouldn't be editable
                                
                                foreach ($admin as $field_name => $field_value) {
                                    if (in_array($field_name, $skip_fields)) {
                                        continue;
                                    }
                                    
                                    $is_readonly = in_array($field_name, $readonly_fields);
                                    $readonly_attr = $is_readonly ? 'readonly' : '';
                                    $readonly_class = $is_readonly ? 'bg-gray-100 cursor-not-allowed' : '';
                                    
                                    // Format field name for display
                                    $display_name = ucfirst(str_replace('_', ' ', $field_name));
                                    
                                    // Determine input type
                                    $input_type = 'text';
                                    if (strpos($field_name, 'email') !== false) {
                                        $input_type = 'email';
                                    } elseif (strpos($field_name, 'phone') !== false) {
                                        $input_type = 'tel';
                                    } elseif (strpos($field_name, 'date') !== false) {
                                        $input_type = 'date';
                                    }
                                ?>
                                    <div class="<?php echo (strpos($field_name, 'address') !== false || strpos($field_name, 'bio') !== false) ? 'md:col-span-2' : ''; ?>">
                                        <label class="block text-sm font-medium text-gray-700" for="<?php echo $field_name; ?>">
                                            <?php echo $display_name; ?>
                                            <?php if ($is_readonly): ?>
                                                <span class="text-xs text-gray-500">(Read-only)</span>
                                            <?php endif; ?>
                                        </label>
                                        <?php if (strpos($field_name, 'address') !== false || strpos($field_name, 'bio') !== false || strpos($field_name, 'description') !== false): ?>
                                            <textarea 
                                                id="<?php echo $field_name; ?>" 
                                                name="<?php echo $field_name; ?>" 
                                                rows="3"
                                                <?php echo $readonly_attr; ?>
                                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 <?php echo $readonly_class; ?>"
                                            ><?php echo htmlspecialchars($field_value ?? ''); ?></textarea>
                                        <?php else: ?>
                                            <input 
                                                type="<?php echo $input_type; ?>" 
                                                id="<?php echo $field_name; ?>" 
                                                name="<?php echo $field_name; ?>" 
                                                value="<?php echo htmlspecialchars($field_value ?? ''); ?>"
                                                <?php echo $readonly_attr; ?>
                                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 <?php echo $readonly_class; ?>"
                                            >
                                        <?php endif; ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6 border-t border-gray-200">
                                <div class="flex space-x-4">
                                    <button type="submit"
                                        class="flex-1 bg-blue-500 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-colors">
                                        Save Changes
                                    </button>
                                    <button type="button" 
                                        onclick="window.location.reload()"
                                        class="flex-1 bg-gray-500 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Display Current Admin Info (Read-only) -->
                        <div class="p-6 border-t border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Admin Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <?php foreach ($admin as $field_name => $field_value): ?>
                                    <div>
                                        <span class="font-medium text-gray-600"><?php echo ucfirst(str_replace('_', ' ', $field_name)); ?>:</span>
                                        <span class="text-gray-800 ml-2">
                                            <?php 
                                            if ($field_name === 'password') {
                                                echo '••••••••';
                                            } else {
                                                echo htmlspecialchars($field_value ?? 'Not set'); 
                                            }
                                            ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="drawer-side">
                <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
                <div class="menu bg-gray-800 text-white min-h-full w-80">
                    <!-- Sidebar content here -->
                    <p class="text-4xl btn btn-ghost font-extrabold text-center"><a href="adminPanel.php">Master Admin</a></p>
                    <p class="text-center">P Paribahan</p>
                    <hr class="my-4">
                    <?php include 'adminSideBarOptions.php'; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
