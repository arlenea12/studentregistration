document.addEventListener('DOMContentLoaded', function() {
    var umidInput = document.getElementById('umid');
    var firstNameInput = document.getElementById('first_name');
    var lastNameInput = document.getElementById('last_name');
    var projectTitleInput = document.getElementById('project_title');
    var emailInput = document.getElementById('email');
    var phoneNumberInput = document.getElementById('phone_number');

    // Add event listeners for input validation
    umidInput.addEventListener('input', validateUmid);
    firstNameInput.addEventListener('input', validateFirstName);
    lastNameInput.addEventListener('input', validateLastName);
    projectTitleInput.addEventListener('input', validateProjectTitle);
    emailInput.addEventListener('input', validateEmail);
    phoneNumberInput.addEventListener('input', validatePhoneNumber);
    // Add other event listeners for different inputs

    function validateUmid() {
        var umidValue = umidInput.value;
        var umidRegex = /^\d{8}$/;

        if (!umidValue.match(umidRegex)) {
            showErrorMessage(umidInput, 'Student ID must be 8 digits');
        } else {
            hideErrorMessage(umidInput);
        }
    }

    function validateFirstName() {
        var firstNameValue = firstNameInput.value;
        var nameRegex = /^[a-zA-Z]+$/;

        if (!firstNameValue.match(nameRegex)) {
            showErrorMessage(firstNameInput, 'First name is required and must contain only alphabetic characters');
        } else {
            hideErrorMessage(firstNameInput);
        }
    }

    function validateLastName() {
        var lastNameValue = lastNameInput.value;
        var nameRegex = /^[a-zA-Z]+$/;

        if (!lastNameValue.match(nameRegex)) {
            showErrorMessage(lastNameInput, 'Last name is required and must contain only alphabetic characters');
        } else {
            hideErrorMessage(lastNameInput);
        }
    }

    function validateProjectTitle() {
        var projectTitleValue = projectTitleInput.value;

        if (projectTitleValue.trim() === '') {
            showErrorMessage(projectTitleInput, 'Project title is required');
        } else {
            hideErrorMessage(projectTitleInput);
        }
    }

    function validateEmail() {
        var emailValue = emailInput.value;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValue.match(emailRegex)) {
            showErrorMessage(emailInput, 'Invalid email address format');
        } else {
            hideErrorMessage(emailInput);
        }
    }

    function validatePhoneNumber() {
        var phoneNumberValue = phoneNumberInput.value;
        var phoneRegex = /^\d{3}-\d{3}-\d{4}$/;

        if (!phoneNumberValue.match(phoneRegex)) {
            showErrorMessage(phoneNumberInput, 'Invalid phone number format (should be in the form 999-999-9999)');
        } else {
            hideErrorMessage(phoneNumberInput);
        }
    }

     function showErrorMessage(inputElement, message) {
        var errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        errorDiv.classList.add(inputElement.id + '-error'); // Adding unique class based on input ID
        errorDiv.innerHTML = message;

        var errorContainer;

        if (inputElement.id === 'first_name' || inputElement.id === 'last_name') {
            var lastNameParent = document.getElementById('last_name').parentNode;
            var firstNameError = document.querySelector('.first_name-error'); // Check if 'first_name' error exists
            var lastNameError = document.querySelector('.last_name-error'); // Check if 'last_name' error exists

            if (inputElement.id === 'first_name' && !firstNameError) {
                lastNameParent.parentNode.insertBefore(errorDiv, lastNameParent.nextSibling);
            } else if (inputElement.id === 'last_name' && !lastNameError) {
                lastNameParent.parentNode.insertBefore(errorDiv, lastNameParent.nextSibling);
            } else {
                if (inputElement.id === 'first_name') {
                    firstNameError.innerHTML = message;
                } else {
                    lastNameError.innerHTML = message;
                }
            }
        } else {
            errorContainer = inputElement.nextElementSibling;

            if (!errorContainer || !errorContainer.classList.contains('invalid-feedback')) {
                errorContainer = document.createElement('div');
                errorContainer.classList.add('invalid-feedback');
                inputElement.parentNode.insertBefore(errorContainer, inputElement.nextSibling);
            }

            errorContainer.innerHTML = message;
        }

        inputElement.classList.add('is-invalid');
    }

    // Function to hide error message and remove 'is-invalid' class
    function hideErrorMessage(inputElement) {
        var errorDiv = inputElement.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.innerHTML = '';
            inputElement.classList.remove('is-invalid');
        }
    }

    // Form submission handling
    document.getElementById('register_form').addEventListener('submit', function(event) {
        event.preventDefault();

        // Validate all fields before submitting
        validateUmid();
        validateFirstName();
        validateLastName();
        validateProjectTitle();
        validateEmail();
        validatePhoneNumber();
        // Validate other fields

        // Check if any field has 'is-invalid' class
        var invalidInputs = document.querySelectorAll('.is-invalid');
        if (invalidInputs.length === 0) {
            this.submit();
        }
    });
});