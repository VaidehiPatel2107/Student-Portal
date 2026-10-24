document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.getElementById('student-table-body');
  const searchInput = document.getElementById('search-input');
  const deptFilter = document.getElementById('department-filter');
  const sortSelect = document.getElementById('sort-select');
  const reloadBtn = document.getElementById('reload-btn');
  const errorBtn = document.getElementById('error-btn');
  const loadingSpinner = document.getElementById('loading-spinner');
  const errorAlert = document.getElementById('error-alert');
  const paginationList = document.getElementById('pagination-list');
  const pageIndicator = document.getElementById('page-indicator');

  // 15 Default records (used as fallback for local file:// execution)
  const fallbackStudents = [
    {"id": "25CS001", "name": "Aarav Sharma", "department": "CSE", "semester": 3, "gpa": 8.9},
    {"id": "25CS014", "name": "Bhavya Joshi", "department": "IT", "semester": 3, "gpa": 7.8},
    {"id": "25CS023", "name": "Chirag Dave", "department": "CSE", "semester": 5, "gpa": 8.2},
    {"id": "25CS035", "name": "Dipti Shah", "department": "BCA", "semester": 1, "gpa": 9.1},
    {"id": "25CS042", "name": "Eshan Trivedi", "department": "IT", "semester": 3, "gpa": 7.4},
    {"id": "25CS051", "name": "Forum Panchal", "department": "CSE", "semester": 5, "gpa": 8.5},
    {"id": "25CS067", "name": "Gaurav Modi", "department": "BCA", "semester": 3, "gpa": 7.9},
    {"id": "25CS074", "name": "Harshil Vyas", "department": "IT", "semester": 1, "gpa": 8.0},
    {"id": "25CS081", "name": "Vaidehi Patel", "department": "CSE", "semester": 3, "gpa": 9.4},
    {"id": "25CS092", "name": "Ishani Parekh", "department": "CSE", "semester": 5, "gpa": 8.7},
    {"id": "25CS103", "name": "Jatin Makwana", "department": "IT", "semester": 3, "gpa": 7.2},
    {"id": "25CS115", "name": "Kavya Desai", "department": "BCA", "semester": 1, "gpa": 8.8},
    {"id": "25CS126", "name": "Manan Soni", "department": "CSE", "semester": 3, "gpa": 7.6},
    {"id": "25CS138", "name": "Neha Parmar", "department": "IT", "semester": 5, "gpa": 8.3},
    {"id": "25CS149", "name": "Parth Zala", "department": "BCA", "semester": 3, "gpa": 8.1}
  ];

  let rawStudents = [];
  let processedStudents = [];
  let currentPage = 1;
  const recordsPerPage = 5;

  // 1. Fetch API Handler with Local Fallback
  async function fetchStudents(endpoint = 'data/students.json') {
    loadingSpinner.classList.remove('d-none');
    errorAlert.classList.add('d-none');
    tableBody.innerHTML = '';
    paginationList.innerHTML = '';
    pageIndicator.textContent = '';

    try {
      let data;
      // Intentionally trigger error when Test Error button is clicked
      if (endpoint.includes('missing-endpoint')) {
        const response = await fetch(endpoint);
        if (!response.ok) throw new Error(`HTTP ${response.status}: Resource Not Found`);
        data = await response.json();
      } else {
        // Attempt normal Fetch
        try {
          const response = await fetch(endpoint);
          if (!response.ok) throw new Error(`HTTP ${response.status}`);
          data = await response.json();
        } catch {
          // Automatic fallback for local file:// protocol
          data = fallbackStudents;
        }
      }

      rawStudents = data;
      applyFiltersAndRender();
    } catch (err) {
      errorAlert.textContent = `⚠️ Network Error: ${err.message || 'Failed to fetch resource.'}`;
      errorAlert.classList.remove('d-none');
    } finally {
      loadingSpinner.classList.add('d-none');
    }
  }

  // 2. Filter, Search, and Sort Processor
  function applyFiltersAndRender() {
    const query = searchInput.value.toLowerCase().trim();
    const selectedDept = deptFilter.value;
    const sortMode = sortSelect.value;

    processedStudents = rawStudents.filter((student) => {
      const matchesSearch =
        student.name.toLowerCase().includes(query) ||
        student.id.toLowerCase().includes(query);
      const matchesDept = selectedDept === 'ALL' || student.department === selectedDept;
      return matchesSearch && matchesDept;
    });

    // Sorting
    processedStudents.sort((a, b) => {
      if (sortMode === 'name-asc') return a.name.localeCompare(b.name);
      if (sortMode === 'name-desc') return b.name.localeCompare(a.name);
      if (sortMode === 'gpa-desc') return b.gpa - a.gpa;
      if (sortMode === 'gpa-asc') return a.gpa - b.gpa;
      return 0;
    });

    currentPage = 1;
    renderTable();
    renderPagination();
  }

  // 3. Dynamic Rendering with Pagination
  function renderTable() {
    tableBody.innerHTML = '';

    if (processedStudents.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No student records found matching the query.</td></tr>`;
      pageIndicator.textContent = 'Showing 0 records';
      return;
    }

    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = Math.min(startIndex + recordsPerPage, processedStudents.length);
    const paginatedSlice = processedStudents.slice(startIndex, endIndex);

    paginatedSlice.forEach((student) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="fw-semibold text-primary">${student.id}</td>
        <td>${student.name}</td>
        <td><span class="badge bg-secondary">${student.department}</span></td>
        <td>Semester ${student.semester}</td>
        <td class="fw-bold">${student.gpa.toFixed(1)}</td>
      `;
      tableBody.appendChild(row);
    });

    pageIndicator.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${processedStudents.length} records`;
  }

  // 4. Pagination Controls
  function renderPagination() {
    paginationList.innerHTML = '';
    const totalPages = Math.ceil(processedStudents.length / recordsPerPage);

    if (totalPages <= 1) return;

    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement('li');
      li.className = `page-item ${i === currentPage ? 'active' : ''}`;
      li.innerHTML = `<button class="page-link">${i}</button>`;
      li.addEventListener('click', () => {
        currentPage = i;
        renderTable();
        renderPagination();
      });
      paginationList.appendChild(li);
    }
  }

  // Event Listeners
  searchInput.addEventListener('input', applyFiltersAndRender);
  deptFilter.addEventListener('change', applyFiltersAndRender);
  sortSelect.addEventListener('change', applyFiltersAndRender);

  reloadBtn.addEventListener('click', () => {
    searchInput.value = '';
    deptFilter.value = 'ALL';
    sortSelect.value = 'name-asc';
    fetchStudents();
  });

  errorBtn.addEventListener('click', () => {
    fetchStudents('data/missing-endpoint.json');
  });

  // Initial Load
  fetchStudents();
});