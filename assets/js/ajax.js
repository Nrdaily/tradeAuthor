// Example of registration function in JavaScript
function registerUser(userData) {
    fetch('http://yourdomain.com/backend/api/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            if (data.message === "User was created.") {
                // Redirect to login or dashboard
                window.location.href = 'login.html';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Example usage
const userData = {
    email: 'john.doe@example.com',
    password: 'securepassword123',
    first_name: 'John',
    last_name: 'Doe',
    phone: '123-456-7890'
};

registerUser(userData);// Example of login function in JavaScript
function loginUser(credentials) {
    fetch('http://yourdomain.com/backend/api/login.php', {  
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            if (data.message === "Login successful.") {
                // Store token and redirect to dashboard
                localStorage.setItem('token', data.token);
                window.location.href = 'dashboard.html';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Example usage
const credentials = {
    email: 'john@gmail.com",
    password: 'securepassword123'
};
loginUser(credentials);