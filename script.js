const API_URL = "api.php";

const studentForm = document.getElementById("studentForm");
const studentIdInput = document.getElementById("studentId");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const courseInput = document.getElementById("course");
const submitBtn = document.getElementById("submitBtn");
const formTitle = document.getElementById("formTitle");
const tableBody = document.getElementById("studentTableBody");
const loadingIndicator = document.getElementById("loadingIndicator");
const alertBox = document.getElementById("alertBox");
const refreshBtn = document.getElementById("refreshBtn");

function showAlert(message, type = "success") {
    alertBox.className = `alert alert-${type}`;
    alertBox.textContent = message;
    alertBox.classList.remove("d-none");
    setTimeout(() => alertBox.classList.add("d-none"), 4000);
}

// 1. READ: Fetch all students
async function loadStudents() {
    loadingIndicator.classList.remove("d-none");
    tableBody.innerHTML = "";

    try {
        const response = await fetch(API_URL);
        const result = await response.json();

        loadingIndicator.classList.add("d-none");

        if (result.status && result.data.length > 0) {
            result.data.forEach(student => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${student.id}</td>
                    <td><strong>${escapeHtml(student.name)}</strong></td>
                    <td>${escapeHtml(student.email)}</td>
                    <td><span class="badge bg-secondary">${escapeHtml(student.course)}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1" onclick="editStudent(${student.id}, '${escapeHtml(student.name)}', '${escapeHtml(student.email)}', '${escapeHtml(student.course)}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">No student records found.</td></tr>`;
        }
    } catch (err) {
        loadingIndicator.classList.add("d-none");
        showAlert("Failed to connect to API endpoint.", "danger");
    }
}

// 2. CREATE or UPDATE
studentForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = studentIdInput.value;
    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const course = courseInput.value;

    if (!name || !email) {
        showAlert("Name and Email are required fields!", "danger");
        return;
    }

    const payload = { id, name, email, course };
    const method = id ? "PUT" : "POST";

    try {
        const res = await fetch(API_URL, {
            method: method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok && data.status) {
            showAlert(data.message, "success");
            resetForm();
            loadStudents();
        } else {
            showAlert(data.message || "Operation failed.", "danger");
        }
    } catch (err) {
        showAlert("Network communication error.", "danger");
    }
});

// 3. EDIT Handler
window.editStudent = function(id, name, email, course) {
    studentIdInput.value = id;
    nameInput.value = name;
    emailInput.value = email;
    courseInput.value = course;
    submitBtn.textContent = "Update";
    formTitle.textContent = "Edit Student Details";
    submitBtn.classList.replace("btn-primary", "btn-warning");
};

// 4. DELETE Handler
window.deleteStudent = async function(id) {
    if (!confirm(`Are you sure you want to delete student ID ${id}?`)) return;

    try {
        const res = await fetch(`${API_URL}?id=${id}`, {
            method: "DELETE"
        });
        const data = await res.json();

        if (res.ok && data.status) {
            showAlert(data.message, "success");
            loadStudents();
        } else {
            showAlert(data.message || "Failed to delete.", "danger");
        }
    } catch (err) {
        showAlert("Error connecting to server.", "danger");
    }
};

function resetForm() {
    studentIdInput.value = "";
    studentForm.reset();
    submitBtn.textContent = "Save";
    formTitle.textContent = "Add New Student";
    submitBtn.classList.replace("btn-warning", "btn-primary");
}

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

refreshBtn.addEventListener("click", loadStudents);
document.addEventListener("DOMContentLoaded", loadStudents);