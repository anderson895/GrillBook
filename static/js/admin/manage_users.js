class UserManager {
    constructor() {
        this.users = [];
        this.filteredUsers = [];
        this.currentSort = { field: 'user_id', direction: 'asc' };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadUsers();
    }

    bindEvents() {
        // Search functionality with debounce
        $('#searchInput').on('input', this.debounce(() => this.filterUsers(), 300));
        
        // Refresh button
        $('#refreshBtn').on('click', () => this.loadUsers());
        
        // Sort functionality
        $('.sortable').on('click', (e) => {
            const field = $(e.currentTarget).data('sort');
            this.sortUsers(field);
        });
        
        // Modal close
        $('#closeModal').on('click', () => this.hideModal());
        
        // Close modal on background click
        $('#userModal').on('click', (e) => {
            if (e.target.id === 'userModal') this.hideModal();
        });

        // Keyboard shortcuts
        $(document).on('keydown', (e) => {
            if (e.key === 'Escape') this.hideModal();
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.loadUsers();
            }
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    showSpinner() {
        $('#spinner').removeClass('hidden');
    }

    hideSpinner() {
        $('#spinner').addClass('hidden');
    }

    showModal() {
        $('#userModal').removeClass('hidden').addClass('flex');
    }

    hideModal() {
        $('#userModal').removeClass('flex').addClass('hidden');
    }

    showToast(message, type = 'success') {
        const toast = $('#toast');
        const toastIcon = $('#toastIcon');
        const toastMessage = $('#toastMessage');
        
        const styles = {
            success: {
                class: 'toast-success',
                icon: 'check_circle'
            },
            error: {
                class: 'toast-error',
                icon: 'error'
            },
            warning: {
                class: 'toast-warning',
                icon: 'warning'
            }
        };
        
        const style = styles[type] || styles.success;
        
        toast.removeClass().addClass(style.class);
        toastIcon.text(style.icon);
        toastMessage.text(message);
        
        toast.removeClass('hidden');
        
        setTimeout(() => {
            toast.addClass('hidden');
        }, 4000);
    }

    loadUsers() {
        this.showSpinner();
        
        $.ajax({
            url: "../controller/end-points/controller.php",
            method: "GET",
            data: { requestType: "fetch_all_users" },
            dataType: "json",
            success: (res) => {
                this.hideSpinner();
                if (res.status === 200) {
                    this.users = res.data;
                    this.filteredUsers = [...this.users];
                    this.renderUsers();
                    this.updateResultsCount();
                    this.showToast('Users loaded successfully', 'success');
                } else {
                    this.showToast('Failed to load users', 'error');
                }
            },
            error: (xhr, status, error) => {
                this.hideSpinner();
                this.showToast('Error loading users. Please try again.', 'error');
                console.error('Error loading users:', error);
            }
        });
    }

    filterUsers() {
        const searchTerm = $('#searchInput').val().toLowerCase().trim();
        
        if (!searchTerm) {
            this.filteredUsers = [...this.users];
        } else {
            this.filteredUsers = this.users.filter(user => 
                user.user_fname.toLowerCase().includes(searchTerm) ||
                user.user_lname.toLowerCase().includes(searchTerm) ||
                user.user_email.toLowerCase().includes(searchTerm) ||
                user.user_position.toLowerCase().includes(searchTerm)
            );
        }
        
        this.renderUsers();
        this.updateResultsCount();
    }

    sortUsers(field) {
        if (this.currentSort.field === field) {
            this.currentSort.direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.currentSort.field = field;
            this.currentSort.direction = 'asc';
        }

        this.filteredUsers.sort((a, b) => {
            let aVal = a[field];
            let bVal = b[field];
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (aVal < bVal) return this.currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        this.renderUsers();
        this.updateSortIndicators();
    }

    updateSortIndicators() {
        $('.sortable').each(function() {
            const field = $(this).data('sort');
            let indicator = $(this).find('.sort-indicator');
            
            if (indicator.length === 0) {
                indicator = $('<span class="sort-indicator material-icons text-xs">unfold_more</span>');
                $(this).find('div').append(indicator);
            }
            
            if (field === userManager.currentSort.field) {
                indicator.text(userManager.currentSort.direction === 'asc' ? 'arrow_drop_up' : 'arrow_drop_down');
                indicator.addClass('text-[#D4AF37]');
            } else {
                indicator.text('unfold_more');
                indicator.removeClass('text-[#D4AF37]');
            }
        });
    }

    renderUsers() {
        const tbody = $('#userTableBody');
        const noResults = $('#noResults');
        
        tbody.empty();
        
        if (this.filteredUsers.length === 0) {
            noResults.removeClass('hidden');
            return;
        }
        
        noResults.addClass('hidden');
        
        this.filteredUsers.forEach((user, index) => {
            const positionClass = `position-${user.user_position}`;
            const positionLabel = user.user_position.charAt(0).toUpperCase() + user.user_position.slice(1);
            
            const row = $(`
                <tr class="table-row hover:bg-opacity-50 transition-all duration-300">
                    <td class="table-cell font-mono text-sm">#${user.user_id}</td>
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#D4AF37] to-[#B8860B] rounded-full flex items-center justify-center text-sm font-bold text-readable-dark">
                                ${user.user_fname.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="text-readable font-semibold">${user.user_fname} ${user.user_lname}</div>
                                <div class="text-readable-muted text-xs">ID: ${user.user_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell-muted">${user.user_email}</td>
                    <td class="table-cell">
                        <span class="position-badge ${positionClass}">
                            ${positionLabel}
                        </span>
                    </td>
                    <td class="table-cell">
                        <div class="flex gap-2">
                            <button class="viewBtn btn-info text-xs"
                                    data-user_id='${user.user_id}'
                                    title="View user details">
                                <span class="material-icons text-xs">visibility</span>
                                View
                            </button>
                            <button class="removeBtn btn-danger text-xs"
                                    data-user_id='${user.user_id}'
                                    data-user_fname='${user.user_fname}'
                                    data-user_lname='${user.user_lname}'
                                    title="Remove user from system">
                                <span class="material-icons text-xs">delete</span>
                                Remove
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            
            tbody.append(row);
        });
    }

    updateResultsCount() {
        const totalUsers = this.users.length;
        const shownUsers = this.filteredUsers.length;
        
        $('#totalUsers').text(totalUsers);
        $('#totalCount').text(totalUsers);
        $('#shownCount').text(shownUsers);
        
        $('#resultsInfo').toggleClass('hidden', totalUsers === 0);
    }

    showUserDetails(userId) {
        const user = this.users.find(u => u.user_id == userId);
        if (!user) return;
        
        $('#modalTitle').text('User Details');
        $('#modalContent').html(`
            <div class="space-y-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-[#D4AF37] to-[#B8860B] rounded-full flex items-center justify-center text-2xl font-bold text-readable-dark">
                        ${user.user_fname.charAt(0).toUpperCase()}${user.user_lname.charAt(0).toUpperCase()}
                    </div>
                </div>
                
                <div class="grid gap-4 text-sm">
                    <div class="flex justify-between items-center p-3 bg-white bg-opacity-5 rounded-lg">
                        <span class="text-readable-muted font-medium">Full Name:</span>
                        <span class="text-readable font-semibold">${user.user_fname} ${user.user_lname}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white bg-opacity-5 rounded-lg">
                        <span class="text-readable-muted font-medium">Email:</span>
                        <span class="text-readable">${user.user_email}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white bg-opacity-5 rounded-lg">
                        <span class="text-readable-muted font-medium">Role:</span>
                        <span class="position-badge position-${user.user_position} text-xs">
                            ${user.user_position.charAt(0).toUpperCase() + user.user_position.slice(1)}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white bg-opacity-5 rounded-lg">
                        <span class="text-readable-muted font-medium">User ID:</span>
                        <span class="text-readable font-mono">${user.user_id}</span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-[#D4AF37] border-opacity-20">
                    <p class="text-xs text-readable-muted text-center">
                        User account information
                    </p>
                </div>
            </div>
        `);
        
        this.showModal();
    }

    removeUser(userId, userFname, userLname) {
        const userName = `${userFname} ${userLname}`;
        
        Swal.fire({
            title: `Remove User`,
            html: `Are you sure you want to remove <span class="text-[#D4AF37] font-bold">${userName}</span> from the system?`,
            text: 'This action cannot be undone.',
            icon: 'warning',
            background: '#2A2A2A',
            color: '#FFFFFF',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove user',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            customClass: {
                popup: 'user-card',
                title: 'text-readable',
                htmlContainer: 'text-readable-muted',
                confirmButton: 'btn-danger',
                cancelButton: 'bg-gray-600 hover:bg-gray-700'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.showSpinner();
                
                $.ajax({
                    url: "../controller/end-points/controller.php",
                    type: 'POST',
                    data: { user_id: userId, requestType: 'removeUser' },
                    dataType: 'json',
                    success: (response) => {
                        this.hideSpinner();
                        if (response.status === 200) {
                            this.showToast(`User ${userName} removed successfully`, 'success');
                            this.loadUsers(); // Reload the user list
                        } else {
                            this.showToast(response.message || 'Failed to remove user', 'error');
                        }
                    },
                    error: (xhr, status, error) => {
                        this.hideSpinner();
                        this.showToast('Error removing user. Please try again.', 'error');
                        console.error('Error removing user:', error);
                    }
                });
            }
        });
    }
}

// Initialize UserManager when document is ready
let userManager;

$(document).ready(function() {
    userManager = new UserManager();
    
    // Event delegation for dynamic elements
    $(document).on('click', '.viewBtn', function() {
        const userId = $(this).data('user_id');
        userManager.showUserDetails(userId);
    });
    
    $(document).on('click', '.removeBtn', function() {
        const userId = $(this).data('user_id');
        const userFname = $(this).data('user_fname');
        const userLname = $(this).data('user_lname');
        userManager.removeUser(userId, userFname, userLname);
    });
});

// Export for global access (if needed)
window.UserManager = UserManager;