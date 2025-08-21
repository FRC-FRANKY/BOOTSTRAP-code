// Default selection
selectRole('employee');

function selectRole(role) {
    document.getElementById('role').value = role;

    const employeeCard = document.getElementById('employeeCard');
    const employerCard = document.getElementById('employerCard');
    const employeeFields = document.getElementById('employeeFields');
    const employerFields = document.getElementById('employerFields');

    if (role === 'employee') {
        employeeCard.classList.add('selected');
        employerCard.classList.remove('selected');
        employeeFields.style.display = 'block';
        employerFields.style.display = 'none';
    } else {
        employerCard.classList.add('selected');
        employeeCard.classList.remove('selected');
        employerFields.style.display = 'block';
        employeeFields.style.display = 'none';
    }
}
