<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h2 class="mb-4 text-center">User Records</h2>
        <a href="/user" class="btn btn-primary float-end mb-3">Add Student</a>

        <!-- Search and Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search-input" class="form-control" placeholder="Search by name or email">
            </div>
            <div class="col-md-4">
                <select id="gender-filter" class="form-select">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Interests</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body"></tbody>
        </table>

        <!-- Pagination -->
        <nav id="pagination" class="d-flex justify-content-center mt-3" aria-label="Page navigation">
            <ul class="pagination"></ul>
        </nav>
    </div>

    <script>
        const limit = 5;
        let offset = 0; 
        let currentPage = 1; 
        let totalRows = 0; 

        function loadData() {
            const search = $('#search-input').val();
            const gender = $('#gender-filter').val();

            $.ajax({
                url: '/user/fetchPaginatedData',
                method: 'GET',
                data: {
                    limit: limit,
                    offset: offset,
                    search: search,
                    gender: gender
                },
                success: function(response) {
                    if (response.status) {
                        const users = response.data || [];
                        totalRows = response.total_rows || 0;

                        const tableBody = $('#user-table-body');
                        tableBody.empty();

                        users.forEach(function(user) {
                            tableBody.append(`
                                <tr id="user-row-${user.id}">
                                    <td>${user.id}</td>
                                    <td>${user.full_name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.gender}</td>
                                    <td>${user.interests}</td>
                                    <td>${user.country}</td>
                                    <td>
                                        <a href="/user?id=${user.id}" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button>
                                    </td>
                                </tr>
                            `);
                        });

                        updatePagination();
                    } else {
                        alert('Failed to fetch data.');
                    }
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });
        }

        function updatePagination() {
            const totalPages = Math.ceil(totalRows / limit);
            const pagination = $('#pagination ul');
            pagination.empty();

            pagination.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link" onclick="changePage(${currentPage - 1})">&laquo;</button>
                </li>
            `);

            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <button class="page-link" onclick="changePage(${i})">${i}</button>
                    </li>
                `);
            }

            pagination.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link" onclick="changePage(${currentPage + 1})">&raquo;</button>
                </li>
            `);
        }

        function changePage(page) {
            const totalPages = Math.ceil(totalRows / limit);

            if (page < 1 || page > totalPages) return;

            currentPage = page;
            offset = (page - 1) * limit;
            loadData();
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: `/delete-user/${id}`,
                    method: "POST",
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            loadData();
                        } else {
                            alert('Failed to delete user.');
                        }
                    },
                    error: function() {
                        alert('Error deleting user.');
                    }
                });
            }
        }

        function applyFilters() {
            currentPage = 1; 
            offset = 0; 
            loadData();
        }

        $(document).ready(function() {
            loadData();
        });
    </script>
</body>

</html>
