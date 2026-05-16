const actualPassword = $user['password'];

    // Password toggle logic
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        if (passwordField.textContent === "********") {
            // Show the actual password
            passwordField.textContent = actualPassword;
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            // Hide the password
            passwordField.textContent = "********";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
// Change Password Function
function changePassword() {
    alert("Password change functionality will be added!");
    // You can redirect to a PHP page for password change or use a modal form here.
}

// Other Profile Actions
function editProfile() {
    alert("Edit profile functionality coming soon!");
}

function logout() {
    alert("Logging out...");
    // Handle logout functionality in PHP.
}
