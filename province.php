<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Philippines Location Selector</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    select, input {
      display: block;
      margin: 10px 0;
      padding: 10px;
      width: 300px;
    }
    label {
      font-weight: bold;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h1>Philippines Location Selector</h1>

  <label for="province">Province:</label>
  <select id="province">
    <option value="">-- Select Province --</option>
  </select>

  <label for="municipality">Municipality/City:</label>
  <select id="municipality" disabled>
    <option value="">-- Select Municipality/City --</option>
  </select>

  <label for="barangay">Barangay:</label>
  <select id="barangay" disabled>
    <option value="">-- Select Barangay --</option>
  </select>

  <label for="sitio">Sitio (Optional - Type Manually):</label>
  <input type="text" id="sitio" placeholder="Enter Sitio Name">

  <script>
    // API Base URL
    const BASE_URL = "https://psgc.gitlab.io/api";

    // DOM Elements
    const provinceSelect = document.getElementById("province");
    const municipalitySelect = document.getElementById("municipality");
    const barangaySelect = document.getElementById("barangay");

    // Fetch Provinces
    function fetchProvinces() {
      fetch(`${BASE_URL}/provinces/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(province => {
            const option = document.createElement("option");
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
          });
        })
        .catch(error => console.error("Error fetching provinces:", error));
    }

    // Fetch Municipalities for a Selected Province
    function fetchMunicipalities(provinceCode) {
      municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
      barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
      municipalitySelect.disabled = true;
      barangaySelect.disabled = true;

      fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(municipality => {
            const option = document.createElement("option");
            option.value = municipality.code;
            option.textContent = municipality.name;
            municipalitySelect.appendChild(option);
          });
          municipalitySelect.disabled = false;
        })
        .catch(error => console.error("Error fetching municipalities:", error));
    }

    // Fetch Barangays for a Selected Municipality
    function fetchBarangays(municipalityCode) {
      barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
      barangaySelect.disabled = true;

      fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(barangay => {
            const option = document.createElement("option");
            option.value = barangay.code;
            option.textContent = barangay.name;
            barangaySelect.appendChild(option);
          });
          barangaySelect.disabled = false;
        })
        .catch(error => console.error("Error fetching barangays:", error));
    }

    // Event Listeners
    provinceSelect.addEventListener("change", () => {
      const provinceCode = provinceSelect.value;
      if (provinceCode) {
        fetchMunicipalities(provinceCode);
      } else {
        municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        municipalitySelect.disabled = true;
        barangaySelect.disabled = true;
      }
    });

    municipalitySelect.addEventListener("change", () => {
      const municipalityCode = municipalitySelect.value;
      if (municipalityCode) {
        fetchBarangays(municipalityCode);
      } else {
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        barangaySelect.disabled = true;
      }
    });

    // Initialize Provinces on Page Load
    fetchProvinces();
  </script>
</body>
</html>
