<?php
include 'config.php'; // Connect to MySQL

// Fetch all resources
//$result = $conn->query("SELECT * FROM resources ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Micro OSS App</title>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-gray-100">

		<!-- Navigation Menu -->
		  <div class="sticky-header">
			<?php include('includes/nav.php'); ?>
		</div>


<h2>Barangay Resources</h2>

<!-- Filter Dropdown -->
<label for="filter">Filter by Category:</label>
<select id="filter">
    <option value="all">All</option>
    <option value="Policy Brief">Policy Brief</option>
    <option value="Media Release">Media Release</option>
    <option value="Infographic">Infographic</option>
    <option value="Fact Sheet">Fact Sheet</option>
</select>

<?php include('includes/footer.php'); ?>

</body>
</html>
